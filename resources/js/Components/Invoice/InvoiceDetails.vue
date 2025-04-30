<script setup>
import { ref } from "vue";

const props = defineProps({
    show: Boolean,
    customer: Object,
});

function formatNumberWithCommas(amount) {
    if (!amount) return '0';

    // Convert amount to string and handle negative numbers
    const num = Math.abs(amount).toString();

    // Check if it needs to be formatted with thousands and lakhs
    if (num.length > 3) {
        const lastThreeDigits = num.substring(num.length - 3); // Get the last three digits
        const otherNumbers = num.substring(0, num.length - 3); // Get the rest of the number

        // Format the number with commas based on Indian number system (lakh, thousand)
        const formatted = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + lastThreeDigits;
        return (amount < 0 ? '-' : '') + formatted;
    }

    // If the number is less than a thousand, return it as is
    return (amount < 0 ? '-' : '') + num;
}

const emit = defineEmits(["update:show"]);

const printInvoice = () => {
    const printContent = document.getElementById("print-invoice").innerHTML;

    const printDiv = document.createElement("div");
    printDiv.innerHTML = printContent;

    printDiv.style.position = "absolute";
    printDiv.style.top = "0";
    printDiv.style.left = "0";
    printDiv.style.right = "0";
    printDiv.style.bottom = "0";
    printDiv.style.height = "100%";
    printDiv.style.width = "100%";
    printDiv.style.backgroundColor = "white";
    printDiv.style.zIndex = "9999";
    document.body.appendChild(printDiv);
    window.print();

    document.body.removeChild(printDiv);
};

// const formatDate = (date) => {
//     props.customer.created_at = new Date(props.customer.created_at);
//     return props.customer.created_at.toISOString().slice(0, 10);
// }

const formatDate = (date) => {
    const createdAt = new Date(props.customer.created_at);
    const day = String(createdAt.getDate()).padStart(2, '0'); // get day and add leading zero if needed
    const month = String(createdAt.getMonth() + 1).padStart(2, '0'); // get month (months are 0-indexed) and add leading zero
    const year = createdAt.getFullYear(); // get full year

    return `${day}-${month}-${year}`;
}
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 z-50"
    >
        <div
            class="bg-white border rounded-lg shadow-lg p-6 w-[1000px] overflow-y-auto"
        >


            <div id="print-invoice" class="max-h-[400px] overflow-y-auto">
                <div class="flex justify-between mb-4">
                    <div class="mt-4">
                        <h1 class="text-xl font-bold">Work Order</h1>
                        <p>Date: {{ formatDate(props.customer.created_at) }}</p>
                    </div>
                    <div class="font-bold">
                        <img class="h-[90px]" src="../../Assets/img/logo.jpg" alt="Logo" />
                    </div>
                </div>
                
                <!-- Bill To Information -->
                <div class="mb-4 text-xl">
                    <!-- <h2 class="text-xl font-bold">Bill To:</h2> -->
                    <p><strong>Customer Name:</strong> {{ props.customer.customer.name }}</p>
                    <p><strong>Mobile:</strong> {{ props.customer.customer.mobile }}</p>
                    <p><strong>Invoice No:</strong> {{ props.customer.id }}</p>
                    <p><strong>Delivery Date:</strong> {{customer.delivery_date ? new Date(props.customer.delivery_date).toLocaleDateString():'' }}</p>
                    <p><strong>Delivery Place:</strong> {{ props.customer.delivery_place }}</p>
                </div>

                <!-- Itemized List with Description, Weight, Size, Rate, Qty, and Order Price -->
                <table class="w-full mb-4">
                    <thead>
                        <tr>
                            <th class="text-left">SL</th>
                            <th class="text-left">Product Description</th>
                            <th class="text-left">Weight</th>
                            <th class="text-left">Size</th>
                            <!-- <th class="text-left">Rate</th> -->
                            <th class="text-left">Qty/Pcs</th>
                            <th class="text-left">Qty/Kg</th>
                            <!-- <th class="text-left">Order Price</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, index) in props.customer.invoice_products" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td class="max-w-[300px] break-words  whitespace-normal">{{ product.product.decription }}</td> <!-- Display description here -->
                            <td>{{ product.product.weight }}</td>
                            <td>{{ product.product.size }}</td>
                            <!-- <td>{{ product.rate }}</td> -->
                            <td>{{ product.qty_by_pc }}</td>
                            <td>{{ product.qty_by_kg }}</td>
                            <!-- <td>{{ product.order_price }}</td> -->
                        </tr>
                    </tbody>
                </table>

                <!-- Total, Payable, Vat, Discount -->
                <div class="mt-4 border-t pt-4">
                    <div class="flex">
                        <button class="btn border-primary btn-sm font-normal border-2 mt-3">Total Order By Kg:
                            <strong>{{ formatNumberWithCommas(props.customer.total_by_kg) }}</strong> Kg
                        </button>
                    </div>
                    
                    <div class="flex">
                        <button class="btn border-primary btn-sm font-normal border-2 mt-3">Total Order By Pcs:
                            <strong>{{ formatNumberWithCommas(props.customer.total_by_pc) }}</strong> Pcs
                        </button>
                    </div>
                    <div>
                        <p ><strong class="text-xl">Note:</strong> {{ props.customer.note }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 flex justify-between items-end p-6">
                        <p class="border-t border-black">Prepared By </p>
                        <p class="border-t border-black">Checked By </p>
                        <p class="border-t border-black">Received By </p>
                        <p class="border-t border-black">Authorized By </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between mt-6">
                <button
                    @click="$emit('update:show', false)"
                    class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600"
                >
                    Close
                </button>
                <button
                    @click="[printInvoice(), $emit('update:show', false)]"
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
    border: 2px solid black;
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
.mb-4 {
    margin-bottom: 1rem;
}
.font-bold {
    font-weight: bold;
}
.text-xl {
    font-size: 1.25rem;
}
.text-left {
    text-align: left;
}
.text-center {
    text-align: center;
}
.text-right {
    text-align: right;
}
.flex {
    display: flex;
}
.justify-between {
    justify-content: space-between;
}
.items-center {
    align-items: center;
}

.hover\:bg-red-600:hover {
    background-color: #e53e3e;
}
.hover\:bg-blue-600:hover {
    background-color: #3182ce;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th,td,tr {
    padding: 8px;
    border: 2px solid black;
    text-align: left;
}
</style>
