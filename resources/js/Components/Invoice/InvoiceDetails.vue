<script setup>
import { ref } from "vue";

const props = defineProps({
    show: Boolean,
    customer: Object,
});

const emit = defineEmits(["update:show"]);

const printInvoice = () => {
    const printContent = document.getElementById("print-invoice").innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
};


const formatNumber = (value) => {
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value ?? 0);
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50"
    >
        <div
            class="bg-white border-2 border-black rounded-lg shadow-lg p-6 w-[1000px] max-h-[90vh] overflow-y-auto"
        >
            <div id="print-invoice" class="max-h-[400px] overflow-y-auto">
                <div class="flex justify-between mb-4">
                    <div class="mt-4">
                        <h1 class="text-xl font-bold">Work Order</h1>
                        <p>
                            Date:
                            {{
                                props.customer.created_at
                                    ? new Date(
                                          props.customer.created_at
                                      ).toLocaleDateString()
                                    : ""
                            }}
                        </p>
                    </div>
                    <div class="font-bold">
                        <img
                            class="h-[90px]"
                            src="../../Assets/img/logo.jpg"
                            alt="Logo"
                        />
                    </div>
                </div>

                <div class="mb-4 text-xl">
                    <p>
                        <strong>Customer Name:</strong>
                        {{ props.customer.customer.name }}
                    </p>
                    <p>
                        <strong>Mobile:</strong>
                        {{ props.customer.customer.mobile }}
                    </p>
                    <p><strong>Invoice No:</strong> {{ props.customer.id }}</p>
                    <p>
                        <strong>Delivery Date:</strong>
                        {{
                            props.customer.delivery_date
                                ? new Date(
                                      props.customer.delivery_date
                                  ).toLocaleDateString()
                                : ""
                        }}
                    </p>
                    <p>
                        <strong>Delivery Place:</strong>
                        {{ props.customer.delivery_place }}
                    </p>
                </div>

                <table
                    class="w-full mb-4 border-collapse border-2 border-black"
                >
                    <thead>
                        <tr class="border border-black">
                            <th class="p-2 border border-black text-left">SL</th>
                            <th class="p-2 border border-black text-left">
                                Product Description
                            </th>
                            <th class="p-2 border border-black text-left">
                                Weight
                            </th>
                            <th class="p-2 border border-black text-left">Size</th>
                            <th class="p-2 border border-black text-left">
                                Qty/Pcs
                            </th>
                            <th class="p-2 border border-black text-left">
                                Qty/Kg
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(product, index) in props.customer.invoice_products"
                            :key="index"
                            class="border border-black"
                        >
                            <td class="p-2 border border-black">
                                {{ index + 1 }}
                            </td>
                            <td
                                class="p-2 border border-black max-w-[300px] break-words"
                            >
                                {{ product.product.decription }}
                            </td>
                            <td class="p-2 border border-black">
                                {{ product.product.weight }}
                            </td>
                            <td class="p-2 border border-black">
                                {{ product.product.size }}
                            </td>
                            <td class="p-2 border border-black">
                                {{ product.qty_by_pc }}
                            </td>
                            <td class="p-2 border border-black">
                                {{ product.qty_by_kg }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 border-t pt-4">
                    <div class="flex">
                        <button
                            class="border-2 border-primary text-sm font-normal mt-3 px-4 py-1 rounded"
                        >
                            Total Order By Kg:
                            <strong>{{
                                formatNumber(props.customer.total_by_kg)
                            }}</strong>
                            Kg
                        </button>
                    </div>
                    <div class="flex">
                        <button
                            class="border-2 border-primary text-sm font-normal mt-3 px-4 py-1 rounded"
                        >
                            Total Order By Pcs:
                            <strong>{{
                                formatNumber(props.customer.total_by_pc)
                            }}</strong>
                            Pcs
                        </button>
                    </div>

                    <div class="mt-4">
                        <p>
                            <strong class="text-xl">Note:</strong>
                            {{ props.customer.note }}
                        </p>
                    </div>

                    <div
                        class="flex justify-between mt-6 relative print:absolute print:bottom-0 print:left-0 print:right-0 print:px-6 print:pb-6"
                    >
                        <p class="border-t border-black pt-1">Prepared By</p>
                        <p class="border-t border-black pt-1">Checked By</p>
                        <p class="border-t border-black pt-1">Received By</p>
                        <p class="border-t border-black pt-1">Authorized By</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button
                    @click="$emit('update:show', false)"
                    class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600"
                >
                    Close
                </button>
                <button
                    @click="[printInvoice(), $emit('update:show', false)]"
                    class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
                >
                    Print
                </button>
            </div>
        </div>
    </div>
</template>
