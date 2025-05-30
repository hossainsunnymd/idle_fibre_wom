<script setup>
import { ref } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster();

const page = usePage();
const searchValue = ref("");
const searchField = ref(["name", "price"]);
const headers = [
  { text: "No", value: "id" },
  { text: "Name", value: "name" },
  { text: "Email", value: "email" },
  { text: "Mobile", value: "mobile" },
  { text: "Role", value: "role" },
  { text: "Action", value: "action" },
];

const items = ref(page.props.users);

const deleteUser = (id) => {
  if (confirm("Are you sure you want to delete this user?")) {
    router.get(`/delete-user?id=${id}`);
  }
};

if (page.props.flash.status === true) {
  toaster.success(page.props.flash.message);
} else if (page.props.flash.status === false) {
  toaster.error(page.props.flash.message);
}
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">User List</h1>

    <input
      v-model="searchValue"
      type="text"
      placeholder="Search..."
      class="mb-5 px-4 py-2 w-72 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
    />

    <EasyDataTable
      buttons-pagination
      alternating
      :headers="headers"
      :items="items"
      :search-value="searchValue"
      :search-field="searchField"
      :rows-per-page="50"
    >
      <template #item-action="{ id }">
        <Link
          :href="`/user-save-page?id=${id}`"
          class="inline-block bg-green-600 hover:bg-green-700 text-white py-2 px-5 rounded transition"
        >
          Edit
        </Link>
        <button
          @click="deleteUser(id)"
          class="inline-block bg-red-600 hover:bg-red-700 ml-3 text-white py-2 px-5 rounded transition"
        >
          Delete
        </button>
      </template>
    </EasyDataTable>

    <div class="mt-8">
      <Link
        :href="`/user-save-page?id=0`"
        class="inline-block bg-orange-600 hover:bg-orange-700 text-white py-3 px-6 rounded transition"
      >
        Create User
      </Link>
    </div>
  </div>
</template>

<style scoped></style>
