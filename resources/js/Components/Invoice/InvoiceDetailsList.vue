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


function formatNumberWithCommas(amount) {
    if (!amount) return '0';

    const num = Math.abs(amount).toString();

    if (num.length > 3) {
        const lastThreeDigits = num.slice(-3);
        let otherNumbers = num.slice(0, num.length - 3);
        let formatted = '';
        while (otherNumbers.length > 2) {

            formatted = ',' + otherNumbers.slice(-2) + formatted;
            otherNumbers = otherNumbers.slice(0, otherNumbers.length - 2);
        }
        formatted = otherNumbers + formatted;
        return (amount < 0 ? '-' : '') + formatted + ',' + lastThreeDigits;
    }
    return (amount < 0 ? '-' : '') + num;
}




const emit = defineEmits(["update:show"]);

const printInvoice = () => {
    const printContent = document.getElementById("print-invoice").innerHTML;
    document.body.innerHTML = ` <html>
        <head>
            <title>Invoice Print</title>
            <style>
  @media print {
    .print-hide {
        display: none !important;
    }

    /* পৃষ্ঠা বিভাজন শুধুমাত্র যেখানে প্রয়োজন */
    .print-page-break {
        page-break-after: auto;
    }

    /* টেবিল ব্রেক না হয় */
    table {
        page-break-inside: auto;
        border: 2px solid black;
    }

    /* প্রতিটি টেবিল row ব্রেক না হয় */
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    /* শুধুমাত্র প্রথম পেইজে টেবিল হেড দেখানোর জন্য */
    thead {
        display: table-header-group;
    }

    /* @page এর মার্জিন ঠিক করা */
    @page {
        margin-top: 0;
        margin-bottom: 0;
    }

    /* সঠিক ব্রেক নিয়ন্ত্রণের জন্য */
    body {
        margin: 0;
        padding: 0;
    }

    /* পুনরাবৃত্তি হেড সরাতে */
    table {
        border-collapse: collapse;
    }

    /* টেবিলের হেড পুনরায় আসতে না দেওয়ার জন্য */
    thead {
        display: table-header-group;
    }

    /* টেবিল স্টাইল */
    th, td {
        padding: 8px;
        text-align: left;
        border: 2px solid black;
    }

    /* হেড ব্রেক নিয়ন্ত্রণ */
    tr {
        page-break-inside: avoid;
    }
}



                body{
                background-color: #ffffff;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th, td,tr {
                    padding: 8px;
                    text-align: center;
                    border: 2px solid black;
                }

                .title {
                    font-size: 20px;
                    font-weight: bold;
                    text-align: center;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>

            <div class="title">
                <h1 class="text-2xl font-bold mt-4">Ideal Fibre Industries Limited</h1>
                <p>Work Order Statement</p>
            </div>


            ${printContent}
        </body>
    </html>
`;

    window.print();
    window.location.reload();
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
                    <!-- <h1 class="text-xl font-bold">১১৩ বি বি রোড, নিতাইগঞ্জ, নারায়ণগঞ্জ</h1> -->
                    <img
                        class="h-[150px]"
                        src="../../Assets/img/logo.jpg"
                        alt="Logo"
                    />

                    <div class="title">
                        Period From: {{ page.props.fromDate ? fromDate : "" }} -
                        {{ page.props.toDate ? toDate : "" }}
                    </div>
                </div>

                <div
                    v-for="(item, i) in props.items"
                    :key="i"
                    class="print-page-break"
                >
                    <table class="w-full border-collapse table-fixed border-t-0">
                        <thead v-if="i === 0">
                            <tr class="bg-gray-200">
                                <th
                                    class="border px-2 py-1 text-center"
                                    style="width: 50px"
                                >
                                    No
                                </th>
                                <!-- Reduced width -->
                                <th
                                    class="border px-2 py-1"
                                    style="width: 180px"
                                >
                                    Customer's Name
                                </th>
                                <th
                                    class="border px-2 py-1"
                                    style="width: 300px"
                                >
                                    Product Description
                                </th>
                                <!-- Increased width -->
                                <th class="border px-2 py-1 text-center" style="width: 80px">Weight</th>
                                <th class="border px-2 py-1 text-center" style="width: 80px">Size</th>
                                <th class="border px-2 py-1 text-center" style="width: 70px">Rate</th>
                                <th class="border px-2 py-1 text-center" style="width: 90px">Qty/Pcs</th>
                                <th class="border px-2 py-1 text-center" style="width: 80px">Qty/Kg</th>
                                <th class="border px-2 py-1 text-center" style="width: 100px">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(
                                    product, index
                                ) in item.invoice_products"
                                :key="index"
                                class="border-b"
                            >
                                <td
                                    v-if="index === 0"
                                    :rowspan="item.invoice_products.length"
                                    class="border-2 px-2 py-1 text-center"
                                    style="
                                        width: 50px;
                                        overflow-wrap: break-word;
                                    "
                                >
                                    {{ i + 1 }}
                                </td>
                                <td
                                    v-if="index === 0"
                                    :rowspan="item.invoice_products.length"
                                    class="border-2 px-2 py-1"
                                    style="width: 180px"
                                >
                                    {{ item.customer.name }}
                                </td>
                                <td
                                    class="border-2 px-2 py-1 overflow-auto"
                                    style="
                                        width: 300px;
                                        max-width: 300px;
                                        word-wrap: break-word;
                                        overflow-wrap: break-word;
                                        white-space: normal;
                                    "
                                >
                                    {{ product.product.decription }}
                                </td>
                                <!-- Increased width -->
                                <td class="border-1 px-2 py-1 text-center" style="width: 80px">
                                    {{ product.product.weight }}
                                </td>
                                <td class="border-1 px-2 py-1 text-center" style="width: 80px">
                                    {{ product.product.size }}
                                </td>
                                <td class="border-1 px-2 py-1 text-center" style="width: 70px">
                                    {{ product.rate }}
                                </td>
                                <td class="border-1 px-2 py-1 text-center" style="width: 90px">
                                    {{ formatNumberWithCommas(product.qty_by_pc) }}
                                </td>
                                <td class="border-1 px-2 py-1 text-center" style="width: 80px">
                                    {{ product.qty_by_kg }}
                                </td>
                                <td class="border-1 px-2 py-1 text-center" style="width: 100px">
                                    {{ formatNumberWithCommas(product.order_price) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-column justify-end">
                    <div>
                        <button class="btn border-primary btn-sm font-normal border-2 mt-3">
                            Total Amount: <strong>{{ formatNumberWithCommas(page.props.total.toLocaleString('en-IN')) }}</strong> BDT
                        </button>
                    </div>
                    <div>
                        <button class="btn border-primary btn-sm font-normal border-2 mt-3">
                            Total Order By Kg: <strong>{{ page.props.totalByKg }}</strong> Kg
                        </button>
                    </div>
                    <div>
                        <button class="btn border-primary btn-sm font-normal border-2 mt-3">
                            Total Order By Piece: <strong>{{ formatNumberWithCommas(parseInt(page.props.totalByPc)) }}</strong>  Pcs
                        </button>
                    </div>

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
.fixed {
    position: fixed;
}
.bg-opacity-50 {
    background-color: rgba(0, 0, 0, 0.5);
}
.z-50 {
    z-index: 50;
}
.bg-white {
    background-color: white;
}
.border {
    border: 1px solid #ddd;
}
.rounded-lg {
    border-radius: 8px;
}
.shadow-lg {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.p-6 {
    padding: 1.5rem;
}
.mt-6 {
    margin-top: 1.5rem;
}
.font-bold {
    font-weight: bold;
}
.text-center {
    text-align: center;
}
.flex {
    display: flex;
}
.justify-between {
    justify-content: space-between;
}
.hover\:bg-red-600:hover {
    background-color: #e53e3e;
}
.hover\:bg-blue-600:hover {
    background-color: #3182ce;
}
table {
    width: 100%;
    /* border-collapse: collapse; */
}
th,
td,
tr {
    padding: 8px;
    border: 2px solid rgb(197, 197, 197);
    text-align: left;
    overflow-wrap: break-word;
}


</style>
