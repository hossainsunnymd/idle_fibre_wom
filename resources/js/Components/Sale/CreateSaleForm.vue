<script setup>
import { computed, ref, reactive } from "vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";

const showModal = ref(false);
const toaster = createToaster({});
const page = usePage();

const productsHeaders = [
    { text: "No", value: "customId" },
    { text: "Description", value: "decription" },
    { text: "Size", value: "size" },
    { text: "Weight", value: "weight" },
    { text: "Rate", value: "rate", sortable: true },
    { text: "Action", value: "action" },
];
const ProductWithProductId = computed(() => {
    return products.value.map((item, index) => ({
        ...item,
        customId: products.value.length - index,
    }));
});

const searchCustomer = ref();
const customerSearchField = ref(["description"]);
const searchProduct = ref();
const productSearchField = ref(["name"]);

const customers = ref(page.props.customers);
const products = ref(page.props.products);

const customersHeaders = [
    { text: "No", value: "customId" },
    { text: "Name", value: "name", sortable: true },
    { text: "Unit", value: "unit" },
    { text: "Action", value: "action" },
];

const CusomerWithCustomId = computed(() => {
    return customers.value.map((item, index) => ({
        ...item,
        customId: customers.value.length - index,
    }));
});

const productList = ref([]);
const productQtyByKg = ref(0);
const productQtyByPc = ref(0);

const product = reactive({
    id: "",
    description: "",
    rate: "",
    size: "",
    weight: "",
    qty_by_kg: "",
    qty_by_pc: "",
});
function addQty(id, decription, rate, size, weight) {
    showModal.value = !showModal.value;
    product.id = id;
    product.description = decription;
    product.rate = rate;
    product.size = size;
    product.weight = weight;
    product.qty_by_kg = productQtyByKg.value;
    product.qty_by_pc = productQtyByPc.value;
}

const addProduct = () => {
    if (productQtyByKg.value == "" && productQtyByPc.value == "") {
        toaster.error("Product Quantity is Required");
        productQtyByPc.value = 0;
        productQtyByKg.value = 0;
        return;
    } else if (
        !Number.isInteger(productQtyByPc.value) ||
        typeof productQtyByKg.value != "number"
    ) {
        toaster.error("Product Quantity is Invalid");
        productQtyByPc.value = 0;
        productQtyByKg.value = 0;
        return;
    } else if (productQtyByKg.value > 0 && productQtyByPc.value > 0) {
        toaster.error("Select Pcs or Kg");
        productQtyByPc.value = 0;
        productQtyByKg.value = 0;
        return;
    }

    const selectedProduct = {
        id: product.id,
        description: product.description,
        rate: product.rate,
        size: product.size,
        weight: product.weight,
        qty_by_kg: productQtyByKg.value,
        qty_by_pc: productQtyByPc.value,
        order_price: productQtyByKg.value
            ? productQtyByKg.value * product.rate
            : productQtyByPc.value * product.rate,
    };
    productList.value.push(selectedProduct);
    productQtyByKg.value = 0;
    productQtyByPc.value = 0;
    calculateTotal();
    showModal.value = !showModal.value;
};

function closeModal() {
    showModal.value = false;
}

const removeProduct = (index) => {
    productList.value.splice(index, 1);
    calculateTotal();
};

const customer = reactive({
    name: "",
    mobile: "",
    id: "",
});

const addCustomer = (name, mobile, id) => {
    customer.name = name;
    customer.mobile = mobile;
    customer.id = id;
};

const calculate = reactive({
    totalByPc: 0,
    totalByKg: 0,
    total: 0,
});

const calculateTotal = () => {
    calculate.total = 0;
    calculate.totalByPc = 0;
    calculate.totalByKg = 0;
    productList.value.forEach((product) => {
        calculate.totalByKg += parseFloat(product.qty_by_kg);
    });
    productList.value.forEach((product) => {
        calculate.totalByPc += parseFloat(product.qty_by_pc);
    });

    productList.value.forEach((product) => {
        calculate.total += parseFloat(product.order_price);
    });
};

const form = useForm({
    cus_id: "",
    products: "",
    total: "",
    total_by_pc: "",
    total_by_kg: "",
    payable: "",
    created_by: page.props.user.name,
    delivery_date: "",
    delivery_place: "",
    note: "",
});

const createInvoice = () => {
    if (customer.name == "") {
        toaster.error("Customer is required");
    } else if (productList.value.length == 0) {
        toaster.error("Product is required");
    } else {
        form.cus_id = customer.id;
        form.products = productList.value;
        form.total = calculate.total;
        form.total_by_pc = calculate.totalByPc;
        form.total_by_kg = calculate.totalByKg;
        form.payable = calculate.payable;

        form.post("create-invoice", {
            onSuccess: () => {
                if (page.props.flash.status === true) {
                    form.reset();
                    productList.value = [];
                    toaster.success(page.props.flash.message);
                    setTimeout(() => {
                        router.get("/invoice-page");
                    }, 500);
                } else {
                    toaster.error(page.props.flash.message);
                }
            },
        });
    }
};
</script>

<template>
    <!-- Modal -->
    <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-black/15 bg-opacity-40 pt-20"
    >
        <div
            class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
        >
            <div
                class="flex justify-between items-center px-4 py-2 rounded-t-lg"
            >
                <h1 id="modal-title" class="text-xl font-bold text-black">
                    Add Quantity
                </h1>
                <button
                    type="button"
                    class="text-white text-2xl font-bold bg-red-500 hover:bg-red-600 rounded-sm w-8 h-8 flex items-center justify-center"
                    @click="closeModal"
                    aria-label="Close"
                >
                    &times;
                </button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label for="qtyKg" class="block font-semibold mb-1"
                        >Kg</label
                    >
                    <input
                        v-model="productQtyByKg"
                        type="number"
                        id="qtyKg"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        min="0"
                    />
                </div>
                <div>
                    <label for="qtyPc" class="block font-semibold mb-1"
                        >Pcs</label
                    >
                    <input
                        v-model="productQtyByPc"
                        type="number"
                        id="qtyPc"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        min="0"
                    />
                </div>
            </div>
            <div class="flex justify-end px-6 py-3 rounded-b-lg">
                <button
                    @click="addProduct"
                    type="button"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded"
                >
                    Add
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-center">Create Work Order</h1>

        <div class="flex flex-col md:flex-row md:space-x-6">
            <!-- Customer Column -->
            <div class="md:w-1/2 mb-6 md:mb-0">
                <div class="border rounded p-4 shadow-sm">
                    <p class="font-semibold mb-3">Select Party</p>
                    <input
                        v-model="searchCustomer"
                        type="text"
                        placeholder="Search Customers..."
                        class="w-full mb-4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <EasyDataTable
                        buttons-paginations
                        alternating
                        :items="CusomerWithCustomId"
                        :headers="customersHeaders"
                        :rows-per-page="10"
                        :search-value="searchCustomer"
                        :seach-field="customerSearchField"
                    >
                        <template #item-action="{ name, mobile, id }">
                            <button
                                @click="addCustomer(name, mobile, id)"
                                type="button"
                                class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded"
                            >
                                Select
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>

            <!-- Product Column -->
            <div class="md:w-1/2">
                <div class="border rounded p-4 shadow-sm">
                    <p class="font-semibold mb-3">Select Product</p>
                    <input
                        v-model="searchProduct"
                        type="text"
                        placeholder="Search Products..."
                        class="w-full mb-4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <EasyDataTable
                        buttons-paginations
                        alternating
                        :items="ProductWithProductId"
                        :headers="productsHeaders"
                        :rows-per-page="10"
                        :search-value="searchProduct"
                        :seach-field="productSearchField"
                    >
                        <template
                            #item-action="{
                                id,
                                decription,
                                rate,
                                size,
                                weight,
                            }"
                        >
                            <button
                                @click="
                                    addQty(id, decription, rate, size, weight)
                                "
                                class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1 rounded"
                            >
                                Select
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>

        <!-- Invoice Section -->
        <div class="mt-10 border rounded p-6 shadow-sm">
            <h5 class="text-right text-lg font-semibold mb-1">Invoice</h5>
            <h6 class="text-right text-sm text-gray-600 mb-4">
                {{ new Date().toISOString().slice(0, 10) }}
            </h6>

            <div class="mb-4">
                <h6 class="font-semibold mb-1">Work Order For:</h6>
                <p>Name: {{ customer.name }}</p>
                <p>Mobile: {{ customer.mobile }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                No
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Description
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Qty/Pc
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Qty/Kg
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Weight
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Size
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Rate
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Total
                            </th>
                            <th
                                class="border border-gray-300 px-2 py-1 text-left text-sm font-semibold"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="productList.length > 0"
                            v-for="(product, index) in productList"
                            :key="index"
                            class="odd:bg-gray-50"
                        >
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ index + 1 }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.description }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.qty_by_pc }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.qty_by_kg }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.weight }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.size }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.rate }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                {{ product.order_price }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-xs"
                            >
                                <button
                                    @click="removeProduct(index)"
                                    class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-else>
                            <td
                                colspan="9"
                                class="text-center py-3 text-gray-600 text-sm"
                            >
                                No product added yet
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold">
                            <td
                                colspan="2"
                                class="border border-gray-300 px-2 py-1 text-sm text-right"
                            >
                                Total
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-sm"
                            >
                                {{ calculate.totalByPc }}
                            </td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-sm"
                            >
                                {{ calculate.totalByKg }}
                            </td>
                            <td
                                colspan="3"
                                class="border border-gray-300 px-2 py-1"
                            ></td>
                            <td
                                class="border border-gray-300 px-2 py-1 text-sm"
                            >
                                {{ calculate.total }}
                            </td>
                            <td class="border border-gray-300 px-2 py-1"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div
                class="mt-6 flex flex-col  2xl:flex-row 2xl:justify-between 2xl:items-center space-y-3 2xl:space-y-0 2xl:space-x-6"
            >
                <div
                    class="flex flex-col sm:flex-row sm:space-x-4 w-full md:w-auto"
                >
                    <input
                        v-model="form.delivery_place"
                        type="text"
                        placeholder="Delivery Place"
                        class="w-full sm:w-64 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <input
                        v-model="form.delivery_date"
                        type="date"
                        class="w-full sm:w-44 mt-3 sm:mt-0 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Note textarea -->
                <textarea
                    v-model="form.note"
                    placeholder="Note"
                    rows="2"
                    class="w-full md:w-96 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>

                <!-- Button -->
                <button
                    @click="createInvoice"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded w-full md:w-auto"
                >
                    Create Work Order
                </button>
            </div>
        </div>
    </div>
</template>
