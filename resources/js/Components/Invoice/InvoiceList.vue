<script setup>
import { ref } from "vue";
import InvoiceDetails from "./InvoiceDetails.vue";
// import InvoiceAllDetails from "./InvoiceAllDetails.vue";
import { Link, usePage, useForm, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
import InvoiceDetailsList from "./InvoiceDetailsList.vue";

const toaster = createToaster({});

const show = ref(false);
const customer = ref();
const page = usePage();

const detail=ref(false);

const searchValue = ref();
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
}else if (page.props.flash.status === false) {
  toaster.error(page.props.flash.message);
}


const showDetails = (id) => {
  show.value = !show.value;
  customer.value = items.value.find((item) => item.id === id);
};

const showAllDetails = () => {
    detail.value = !detail.value
}
const fromDate = new URLSearchParams(window.location.search).get("fromDate");
const toDate = new URLSearchParams(window.location.search).get("toDate");
const searchForm = ref({
  fromDate: fromDate?fromDate:"",
  toDate: toDate?toDate:"",
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
  <div class="p-4 bg-[#f8f8f8] ">
    <div class="row">
      <div class="col-md-6">
        <h1 class="d-flex text-2xl font-bold mb-4">Work Order List / Invoices</h1>
      </div>
    </div>

    <InvoiceDetails v-model:show="show" :customer="customer" />
    <InvoiceDetailsList v-model:show="detail" :items="items" />

    <div class="row mb-1">
      <div class="col-md-2 mb-1">
          <input v-model="searchValue" type="text" class="mb-2 form-control form-control-sm" placeholder="Search Customer.."/>
      </div>
      <div class="col-md-2 mb-1">
        <select v-model="searchForm.customerId" class="form-select form-select-sm">
          <option value="" disabled selected>Select Customer</option>
          <option v-for="customer in page.props.customerList" :key="customer.id" :value="customer.id">{{ customer.name }}</option>
        </select>
      </div>

      <div class="col-md-2 mb-1">
        <input v-model="searchForm.fromDate" type="date" class="form-control form-control-sm" />
      </div>

      <div class="col-md-2 mb-1">
        <input v-model="searchForm.toDate" type="date" class="form-control form-control-sm" />
      </div>

      <div class="col-md-4 mb-2">
        <button @click="search" class="btn btn-success btn-sm mr-1">Search Filter</button>
        <Link href="/invoice-page" class="btn btn-secondary btn-sm mr-1">Clear Search</Link>
        <button class="btn btn-primary btn-sm" @click="showAllDetails()">View all</button>
      </div>
    </div>

    <div class="p-1 bg-[#f8f8f8]">
      <div class="mb-4">
        <Link href="/sale-page" class="bg-orange-600 hover:bg-orange-700 text-white px-2 py-2 square">Create Work Order</Link>
      </div>
      <EasyDataTable
        buttons-pagination
        alternating
        :headers="headers"
        :items="items"
        :search-value="searchValue"
        :search-field="searchField"
        :rows-per-page="20"
      >
        <template #item-action="{ id }">
          <button class="btn btn-outline-dark text-sm px-2 py-1 btn-sm" @click="showDetails(id)">
            <i class="fa fa-eye"></i>
          </button>

          <button v-if="page.props.user.role == 'superadmin'" class="btn btn-danger btn-sm" @click="deleteInvoice(id)"><i class="bi bi-trash"></i></button>
        </template>

        <template #item-created_at="{ created_at }">
          {{ formatDate(created_at) }}
        </template>
        
      </EasyDataTable>
    </div>
  </div>
</template>


<style scoped>

/* General input styles for making elements smaller */
.form-control-sm,
.form-select-sm,
.btn-sm {
    padding: 4px 8px;      /* Smaller padding */
    font-size: 10px;       /* Reduce font size */
    line-height: 18px;     /* Adjust line height */
    border-radius: 0px;    /* Smaller border radius */
}

/* Smaller select dropdown */
.form-select-sm {
    padding-right: 28px; /* Adjust padding for the dropdown icon */
    font-size: 10px;
}

/* Reduce margin around buttons */
.btn-sm {
    margin-right: 3px;
    margin-top: 0px;
    margin-bottom: 2px;
}

/* Custom styling for the button groups */
.btn-group-sm > .btn,
.btn-group-sm > .btn-group {
    padding: 4px 8px;
    font-size: 14px;
}

/* Adjust column layout to make space more compact */
.col-md-2,
.col-md-4 {
    padding-left: 15px;   /* Reduce padding between columns */
    padding-right: 8px;
    max-width: 100%;     /* Ensure inputs fit into grid */
}

/* Input fields should have less width for compact design */
input[type="date"],
select {
    max-width: 100%;
}

/* Specific button styling */
.btn-success.btn-sm,
.btn-secondary.btn-sm,
.btn-primary.btn-sm {
    padding: 3px 10px;
    font-size: 11px;
}

/* Adjust margin for the "View all" button */
.btn-primary.my-1 {
    margin-top: 4px;
    margin-bottom: 4px;
}

/* Adjust EasyDataTable padding */
.easy-data-table {
    padding: 8px;
}

/* Optional: Add a custom smaller font size for table items */
.easy-data-table td,
.easy-data-table th {
    font-size: 14px;
}


</style>
