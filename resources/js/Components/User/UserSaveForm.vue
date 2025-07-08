<script setup>
import { reactive } from 'vue';
import { useForm, usePage, router, Link } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({});
const page = usePage();
const status = reactive({
  title: 'Create User',
  buttonTitle: 'Create User',
});
let params = new URLSearchParams(window.location.search);
let id = params.get('id');

const form = useForm({
  name: '',
  email: '',
  mobile: '',
  role: '',
  password: '',
  id: id
})

let list = page.props.users;

let URL = '/create-user';
if (id !== 0 && list !== null) {
  URL = '/update-user';
  form.name = list.name;
  form.email = list.email;
  form.mobile = list.mobile;
  form.role = list.role;
  status.title = 'Update User';
  status.buttonTitle = 'Update User';
}

const submitForm = () => {

    form.post(URL, {
      preserveScroll: true,
      onSuccess: () => {
        if (page.props.flash.status === true) {
          toaster.success(page.props.flash.message);
          setTimeout(() => {
            router.get("/user-page");
          }, 500);
        }
        else {
          toaster.error(page.props.flash.message)
        }
      }
    });
}
</script>

<template>
  <div class="container mx-auto py-8 px-4">
    <form @submit.prevent="submitForm" class="max-w-md mx-auto bg-white p-8 rounded-md shadow-md relative">
      <div class="absolute top-4 right-4">
        <Link href="/user-page"
          class="inline-block bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-1.5 px-4 rounded transition"
        >
          Back
        </Link>
      </div>

      <h4 class="text-2xl font-bold mb-6 text-center">{{ status.title }}</h4>

      <div class="mb-5">
        <label for="name" class="block mb-2 text-gray-700 font-semibold">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
        />
        <input type="hidden" v-model="form.id" name="id" />
        <p v-if="form.errors.name" class="text-red-600 text-xs mt-1">{{ form.errors.name }}</p>
      </div>

      <div class="mb-5">
        <label for="email" class="block mb-2 text-gray-700 font-semibold">Email</label>
        <input
          v-model="form.email"
          :readonly="list !== null"
          type="email"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white"
        />
        <p v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</p>
      </div>

      <div class="mb-5">
        <label for="mobile" class="block mb-2 text-gray-700 font-semibold">Mobile</label>
        <input
          v-model="form.mobile"
          type="text"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"

        />
        <p v-if="form.errors.mobile" class="text-red-600 text-xs mt-1">{{ form.errors.mobile }}</p>
      </div>

      <div class="mb-5">
        <label for="password" class="block mb-2 text-gray-700 font-semibold">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
        />
        <p v-if="form.errors.password" class="text-red-600 text-xs mt-1">{{ form.errors.password }}</p>
      </div>

      <div class="mb-6">
        <label for="role" class="block mb-2 text-gray-700 font-semibold">Role</label>
        <select
          v-model="form.role"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white transition"
        >
          <option value="" disabled>Select role</option>
          <option value="admin">Admin</option>
        </select>
        <p v-if="form.errors.role" class="text-red-600 text-xs mt-1">{{ form.errors.role }}</p>
      </div>

      <button
        type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 rounded-lg transition"
      >
        {{ status.buttonTitle }}
      </button>
    </form>
  </div>
</template>

<style scoped>
</style>
