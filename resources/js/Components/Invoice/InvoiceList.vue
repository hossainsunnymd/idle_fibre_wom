<script setup>
import { ref } from "vue";
import InvoiceDetails from "./InvoiceDetails.vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
import InvoiceDetailsList from "./InvoiceDetailsList.vue";

const toaster = createToaster({});

const show = ref(false);
const customer = ref();
const page = usePage();

const detail=ref(false);

const searchValue = ref("");
const searchField = ref(["customer.name", "id"]);
const headers = [
  { text: "Invoice no", value: "id" },
  { text: "Party Name", value: "customer.name" },
  { text: "Moblie", value: "customer.mobile" },
  { text: "Order/Pcs", value: "total_by_pc" },
  { text: "Order/Kg", value: "total_by_kg" },
  { text: "Amount", value: "total" },
  { text: "Created By", value: "created_by" },
  { text: "Created Date", value: "created_at" },
  { text: "Action", value: "action" },
];

const items = ref(page.props.list);

const deleteInvoice = (id) => {
  if (confirm("Are you sure you want to delete this Invoice?")) {
    router.get(`/delete-invoice?id=${id}`);
  }
};

if (page.props.flash.status === true) {
  toaster.success(page.props.flash.message);
} else if (page.props.flash.status === false) {
  toaster.error(page.props.flash.message);
}

const showDetails = (id) => {
  show.value = !show.value;
  customer.value = items.value.find((item) => item.id === id);
};

const showAllDetails = () => {
  detail.value = !detail.value;
};

const fromDate = new URLSearchParams(window.location.search).get("fromDate");
const toDate = new URLSearchParams(window.location.search).get("toDate");
const searchForm = ref({
  fromDate: fromDate ? fromDate : "",
  toDate: toDate ? toDate : "",
  customerId: "",
});

const search = () => {
  router.get(
    `/invoice-page?fromDate=${searchForm.value.fromDate}&toDate=${searchForm.value.toDate}&customerId=${searchForm.value.customerId}`
  );
};

const formatDate = (date) => {
  const d = new Date(date).toLocaleString();
  return d;
};
</script>

<template>
  <div class="p-4 bg-gray-100 min-h-screen">
    <div class="mb-6">
      <h1 class="text-2xl font-bold">Work Order List / Invoices</h1>
    </div>

    <InvoiceDetails v-model:show="show" :customer="customer" />
    <InvoiceDetailsList v-model:show="detail" :items="items" />

    <div class="flex flex-wrap gap-2 mb-4">
      <input
        v-model="searchValue"
        type="text"
        placeholder="Search Customer.."
        class="sm:w-48 w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      />

      <select
        v-model="searchForm.customerId"
        class="sm:w-48 w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="" disabled selected>Select Customer</option>
        <option
          v-for="customer in page.props.customerList"
          :key="customer.id"
          :value="customer.id"
        >
          {{ customer.name }}
        </option>
      </select>

      <input
        v-model="searchForm.fromDate"
        type="date"
        class="sm:w-48 w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      />

      <input
        v-model="searchForm.toDate"
        type="date"
        class="sm:w-48 w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      />

      <div class="flex flex-wrap gap-2 sm:flex-row flex-col sm:items-center">
        <button
          @click="search"
          class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-1.5 rounded transition"
        >
          Search Filter
        </button>
        <Link
          href="/invoice-page"
          class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-1.5 rounded transition inline-block text-center"
        >
          Clear Search
        </Link>
        <button
          @click="showAllDetails()"
          class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1.5 rounded transition"
        >
          View all
        </button>
      </div>
    </div>

    <div class="mb-4">
      <Link
        href="/sale-page"
        class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded"
      >
        Create Work Order
      </Link>
    </div>

    <EasyDataTable
      buttons-pagination
      alternating
      :headers="headers"
      :items="items"
      :search-value="searchValue"
      :search-field="searchField"
      :rows-per-page="20"
      class="text-sm"
    >
      <template #item-action="{ id }">
        <button
          class="border border-gray-700 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 transition mr-1"
          @click="showDetails(id)"
        >
          <span class="material-icons text-gray-600 text-sm">visibility</span>
        </button>

        <button
          v-if="page.props.user.role == 'superadmin'"
          class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded"
          @click="deleteInvoice(id)"
        >
            <span class="material-icons text-sm">delete</span>
        </button>
      </template>

      <template #item-created_at="{ created_at }">
        {{ formatDate(created_at) }}
      </template>
    </EasyDataTable>
  </div>
</template>
