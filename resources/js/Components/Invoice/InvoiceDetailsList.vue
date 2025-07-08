<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const props = defineProps({
    show: Boolean,
    items: Array,
});
const fromDate = new Date(page.props.fromDate).toLocaleString().split(",")[0];
const toDate = new Date(page.props.toDate).toLocaleString().split(",")[0];


const emit = defineEmits(["update:show"]);

const printInvoice = () => {
    const printContent = document.getElementById("print-invoice").innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 z-50"
    >
        <div class="bg-white border rounded-lg shadow-lg p-6 w-[1100px]">
            <!-- Scrollable Invoice Content -->
            <div id="print-invoice" class="max-h-[400px] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <img
                        class="h-[150px]"
                        src="../../Assets/img/logo.jpg"
                        alt="Logo"
                    />

                    <div class="title text-center">
                        Period From: {{ page.props.fromDate ? fromDate : "" }} -
                        {{ page.props.toDate ? toDate : "" }}
                    </div>
                </div>

                <div
                    v-for="(item, i) in props.items"
                    :key="i"
                    class="print-page-break"
                >
                    <table
                        class="w-full border-collapse table-fixed border-t-0"
                    >
                        <thead v-if="i === 0">
                            <tr class="bg-gray-200">
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 50px"
                                >
                                    No
                                </th>
                                <th
                                    class="border px-2 py-1 whitespace-normal break-words"
                                    style="width: 180px"
                                >
                                    Customer's Name
                                </th>
                                <th
                                    class="border px-2 py-1 whitespace-normal break-words"
                                    style="width: 300px"
                                >
                                    Product Description
                                </th>
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 80px"
                                >
                                    Weight
                                </th>
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 80px"
                                >
                                    Size
                                </th>
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 70px"
                                >
                                    Rate
                                </th>
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 90px"
                                >
                                    Qty/Pcs
                                </th>
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 80px"
                                >
                                    Qty/Kg
                                </th>
                                <th
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 100px"
                                >
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(product, index) in item.invoice_products"
                                :key="index"
                                class="border-b"
                            >
                                <td
                                    v-if="index === 0"
                                    :rowspan="item.invoice_products.length"
                                    class="border-2 px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 50px; overflow-wrap: break-word;"
                                >
                                    {{ i + 1 }}
                                </td>
                                <td
                                    v-if="index === 0"
                                    :rowspan="item.invoice_products.length"
                                    class="border-2 px-2 py-1 whitespace-normal break-words"
                                    style="width: 180px"
                                >
                                    {{ item.customer.name }}
                                </td>
                                <td
                                    class="border-2 px-2 py-1 whitespace-normal break-words overflow-auto"
                                    style="width: 300px; max-width: 300px;"
                                >
                                    {{ product.product.decription }}
                                </td>
                                <td
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 80px"
                                >
                                    {{ product.product.weight }}
                                </td>
                                <td
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 80px"
                                >
                                    {{ product.product.size }}
                                </td>
                                <td
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 70px"
                                >
                                    {{ product.rate }}
                                </td>
                                <td
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 90px"
                                >
                                    {{ product.qty_by_pc }}
                                </td>
                                <td
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 80px"
                                >
                                    {{ product.qty_by_kg }}
                                </td>
                                <td
                                    class="border px-2 py-1 text-center whitespace-normal break-words"
                                    style="width: 100px"
                                >
                                    {{ product.order_price}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col items-start space-y-3 mt-3">
                    <button
                        class="border border-blue-600 text-blue-600 text-sm font-normal px-4 py-2 rounded hover:bg-blue-100"
                    >
                        Total Amount:
                        <strong>{{

                                page.props.total

                        }}</strong>
                        BDT
                    </button>
                    <button
                        class="border border-blue-600 text-blue-600 text-sm font-normal px-4 py-2 rounded hover:bg-blue-100"
                    >
                        Total Order By Kg:
                        <strong>{{ page.props.totalByKg }}</strong> Kg
                    </button>
                    <button
                        class="border border-blue-600 text-blue-600 text-sm font-normal px-4 py-2 rounded hover:bg-blue-100"
                    >
                        Total Order By Piece:
                        <strong>{{
                            formatNumberWithCommas(
                                parseInt(page.props.totalByPc)
                            )
                        }}</strong>
                        Pcs
                    </button>
                </div>
            </div>

            <!-- Action Buttons (Hidden in Print) -->
            <div class="print-hide flex justify-between mt-6">
                <button
                    @click="$emit('update:show', false)"
                    class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600"
                >
                    Close
                </button>
                <button
                    @click="printInvoice()"
                    class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"
                >
                    Print
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    th,
    td {
        border: 2px solid black !important;
        font-weight: bold !important;
        color: black !important;
    }

    table {
        page-break-inside: auto;
        width: 100% !important;
        border-collapse: collapse !important;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }
}
</style>
