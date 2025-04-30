<script setup>
import SideNavLayout from '../../Layout/SideNavLayout.vue';
import { createToaster } from '@meforma/vue-toaster';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { router, Link } from '@inertiajs/vue3';

const page = usePage();
const toaster = createToaster({ });
const form = useForm({
    name: page.props.profile.name,
    email: page.props.profile.email,
    mobile: page.props.profile.mobile,
    password: page.props.profile.password,
})



</script>

<template>

<!-- component -->
<SideNavLayout>


  <div class="container mx-auto py-8">

    <form @submit.prevent="submitForm"  class="w-full max-w-sm mx-auto bg-white p-8 rounded-md shadow-md">
      <div class="float-end">
          <Link href="/invoice-page" class="btn btn-success mx-3 btn-sm">
          Back
          </Link>
      </div>
      <h1 class="text-2xl font-bold mb-6">My Profile</h1>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
        <input v-model="form.name" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500"
          type="text" id="name" name="name">
          <p class="text-red-500">{{ form.errors.name }}</p>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
        <input v-model="form.email"  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500"
          type="email" id="email" name="email" readonly>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="mobile">Mobile</label>
        <input v-model="form.mobile" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500" readonly
          type="text" id="mobile" name="mobile">
          <p class="text-red-500">{{ form.errors.mobile }}</p>
      </div>
      
    </form>
  </div>

</SideNavLayout>

</template>

<style scoped>

</style>

