<script setup>
import { reactive, computed } from "vue";
import { useForm, usePage, router, Link } from "@inertiajs/vue3";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({});
const page = usePage();

const errors = computed(() => page.props.flash.error || {});
const status = reactive({
    title: "Create Party",
    buttonTitle: "Create Party",
});
let params = new URLSearchParams(window.location.search);
let id = params.get("id");

const form = useForm({
    name: "",
    address: "",
    mobile: "",
    unit: "",
    id: id,
});

let list = page.props.customer;

let URL = "/create-customer";
if (id !== 0 && list !== null) {
    URL = "/update-customer";
    form.name = list.name;
    form.address = list.address;
    form.mobile = list.mobile;
    form.unit = list.unit;
    status.title = "Update Party";
    status.buttonTitle = "Update Party";
}

const submitForm = () => {
    form.post(URL, {
        preserveScroll: true,
        onSuccess: () => {
            if (page.props.flash.status === true) {
                setTimeout(() => {
                    router.get("/customer-page");
                }, 500);
            }
        },
    });
};
</script>

<template>
    <div class="container mx-auto py-8">
        <form
            @submit.prevent="submitForm"
            class="w-full max-w-sm mx-auto bg-white p-8 rounded-md shadow-md"
        >
            <div class="float-end">
                <Link
                    href="/customer-page"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded-md ml-3"
                >
                    Back
                </Link>
            </div>
            <h4 class="text-2xl font-bold mb-6">{{ status.title }}</h4>
            
            <div class="mb-4">
                <label
                    class="block text-gray-700 text-sm font-bold mb-2"
                    for="name"
                    >Name</label
                >
                <input
                    v-model="form.name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500"
                    type="text"
                    id="name"
                    name="name"
                />
                <input type="text" v-model="form.id" hidden name="id" />
                <p v-if="errors.name" class="text-red-500 mt-2 text-sm">
                    {{ errors.name[0] }}
                </p>
            </div>
            <div class="mb-4">
                <label
                    class="block text-gray-700 text-sm font-bold mb-2"
                    for="unit"
                    >Unit</label
                >
                <input
                    v-model="form.unit"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500"
                    type="text"
                />
            </div>

            <div class="mb-4">
                <label
                    class="block text-gray-700 text-sm font-bold mb-2"
                    for="email"
                    >Address</label
                >
                <input
                    v-model="form.address"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500"
                    type="text"
                    id="address"
                    name="address"
                />
            </div>

            <div class="mb-4">
                <label
                    class="block text-gray-700 text-sm font-bold mb-2"
                    for="mobile"
                    >Mobile</label
                >
                <input
                    v-model="form.mobile"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-indigo-500"
                    type="text"
                    id="mobile"
                    name="mobile"
                />
            </div>

            <button
                type="submit"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 rounded-lg transition-colors"
            >
                {{ status.buttonTitle }}
            </button>
        </form>
    </div>
</template>

<style scoped></style>
