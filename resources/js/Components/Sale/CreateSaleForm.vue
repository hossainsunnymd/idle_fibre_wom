<script setup>
import { computed,ref ,reactive} from "vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
const page = usePage();
import { createToaster } from "@meforma/vue-toaster";
// import { parse } from "vue/compiler-sfc";

const showModal=ref(false)
const toaster = createToaster({ });

const productsHeaders = [
    { text: "No", value: "customId" },
    { text: "Description", value: "decription" },
    { text: "Size", value: "size" },
    { text: "Weight", value: "weight" },
    { text: "Rate", value: "rate", sortable: true },
    { text: "Action", value: "action" },
];
const ProductWithProductId = computed(() => {
    return products.value.map((item, index) => ({
        ...item,
        customId: products.value.length - index,
    }))
})

const searchCustomer = ref();
const customerSearchField = ref(["description"]);
const searchProduct = ref();
const productSearchField = ref(["name"]);

const customers = ref(page.props.customers);
const products = ref(page.props.products);

const customersHeaders = [
    { text: "No", value: "customId" },
    { text: "Name", value: "name", sortable: true },
    { text: "Unit", value: "unit" },
    { text: "Action", value: "action" },
];

const CusomerWithCustomId = computed(() => {
    return customers.value.map((item, index) => ({
        ...item,
        customId: customers.value.length - index,
    }))
})

const productList=ref([]);
const productQtyByKg=ref(0);
const productQtyByPc=ref(0);


const product=reactive({
    id:'',
    description:'',
    rate:'',
    size:'',
    weight:'',
    qty_by_kg:'',
    qty_by_pc:''

});
function addQty(id,decription,rate,size,weight){

    showModal.value=!showModal.value;
    product.id=id;
    product.description=decription;
    product.rate=rate;
    product.size=size;
    product.weight=weight;
    product.qty_by_kg=productQtyByKg.value;
    product.qty_by_pc=productQtyByPc.value;

};


const addProduct = () => {

    if(productQtyByKg.value=='' && productQtyByPc.value==''){
        toaster.error('Product Quantity is Required');
        productQtyByPc.value=0;
        productQtyByKg.value=0;
        return ;
    }else if(!Number.isInteger(productQtyByPc.value) || typeof productQtyByKg.value!='number' ){
        toaster.error('Product Quantity is Invalid');
        productQtyByPc.value=0;
        productQtyByKg.value=0;
        return ;
    }else if(productQtyByKg.value>0 && productQtyByPc.value>0){
        toaster.error('Select Pcs or Kg');
        productQtyByPc.value=0;
        productQtyByKg.value=0;
        return ;
    }


   const selectedProduct={
       id:product.id,
       description:product.description,
       rate:product.rate,
       size:product.size,
       weight:product.weight,
       qty_by_kg:productQtyByKg.value,
       qty_by_pc:productQtyByPc.value,
       order_price: productQtyByKg.value?(productQtyByKg.value*product.rate):(productQtyByPc.value*product.rate)

   };
   productList.value.push(selectedProduct);
   productQtyByKg.value=0;
   productQtyByPc.value=0;
   calculateTotal();
   showModal.value=!showModal.value;
};

function closeModal() {
    showModal.value = false;
}


const removeProduct = (index)=>{
    productList.value.splice(index,1);
    calculateTotal();
}


const customer=reactive({
    name:'',
    mobile:'',
    id:''
})

const addCustomer=(name,mobile,id)=>{
    customer.name=name;
    customer.mobile=mobile;
    customer.id=id;
}

const calculate=reactive({
    totalByPc:0,
    totalByKg:0,
    total:0,
})

const calculateTotal=()=>{
    calculate.total=0;
    calculate.totalByPc=0;
    calculate.totalByKg=0;
    productList.value.forEach((product)=>{

    calculate.totalByKg+=parseFloat(product.qty_by_kg);

    });
    productList.value.forEach((product)=>{
    calculate.totalByPc+=parseFloat(product.qty_by_pc);

    });

    productList.value.forEach((product)=>{
    calculate.total+=parseFloat(product.order_price);

    });
}

const form=useForm({
    cus_id:'',
    products:'',
    total:'',
    total_by_pc:'',
    total_by_kg:'',
    payable:'',
    created_by:page.props.user.name,
    delivery_date:'',
    delivery_place:'',
    note:''
})


const createInvoice=()=>{
    if(customer.name==''){
        toaster.error('Customer is required');

    }else if(productList.value.length==0){
        toaster.error('Product is required');
    }else{
    form.cus_id=customer.id;
    form.products=productList.value;
    form.total=calculate.total;
    form.total_by_pc=calculate.totalByPc;
    form.total_by_kg=calculate.totalByKg;
    form.payable=calculate.payable;

    form.post('create-invoice',{
        onSuccess: () => {
            if(page.props.flash.status===true){
                form.reset();
                productList.value=[];
                toaster.success(page.props.flash.message);
                setTimeout(() => {
                    router.get("/invoice-page");
                },500);
            }
            else {
                toaster.error(page.props.flash.message)
            }
        }
    })
}
}

</script>


<template>
    <!-- Main modal -->
    <div v-if="showModal" class="modal fade show" tabindex="-1" style="display: block;">
        <div class="modal-dialog">
            <!-- Modal content -->
            <div class="modal-content">
                <!-- Modal header -->
                <div class="modal-header">
                    <h1 class="modal-title font-bold">Add Quantity</h1>
                    <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div>
                        <label for="qty" class="form-label font-bold">Kg</label>
                        <input v-model="productQtyByKg"  type="number"  name="text" id="qty" class="form-control" />
                    </div>

                    <div>
                        <label for="qty" class="form-label font-bold">Pcs</label>
                        <input v-model="productQtyByPc" type="number"  name="text" id="qty" class="form-control" />
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button @click="addProduct()" type="button" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <h1 class="text-2xl font-bold mb-4 text-center">Create Work Order</h1>

        <div class="row">
            <!-- Customer Column -->
            <div class="col-md-6">

                <div class="border p-3 rounded">
                    <p><strong>Select Party</strong></p>

                    <input v-model="searchCustomer" type="text" class="form-control mb-3 form-control-sm" placeholder="Search Customers..." />
                    <EasyDataTable
                        buttons-paginations
                        alternating
                        :items="CusomerWithCustomId"
                        :headers="customersHeaders"
                        :rows-per-page="10"
                        :search-value="searchCustomer"
                        :seach-field="customerSearchField"
                    >
                        <template #item-action="{name,mobile,id}">
                            <button type="button" class="btn btn-success btn-sm" @click="addCustomer(name,mobile,id)">Select</button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>

            <!-- Product Column -->
            <div class="col-md-6">
                <div class="border p-3 rounded">
                    <p><strong>Select Product</strong></p>
                    <input v-model="searchProduct" type="text" class="form-control mb-3 form-control-sm" placeholder="Search Products..." />
                    <EasyDataTable
                        buttons-paginations
                        alternating
                        :items="ProductWithProductId"
                        :headers="productsHeaders"
                        :rows-per-page="10"
                        :search-value="searchProduct"
                        :seach-field="productSearchField"
                    >
                        <template #item-action="{id,decription,rate,size,weight}">
                            <button class="btn btn-success btn-sm" @click="addQty(id,decription,rate,size,weight)">Select</button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>

        <!-- Invoice Section -->
        <div class="mt-5 border p-3 rounded">
            <h5 class="text-end">Invoice</h5>
            <h6 class="text-end">{{ new Date().toISOString().slice(0, 10) }}</h6>

            <div>
                <h6 class="font-bold">Work Order For:</h6>
                <p>Name: {{ customer.name }}</p>
                <p>Mobile: {{ customer.mobile }}</p>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Description</th>
                        <th>Qty/Pc</th>
                        <th>Qty/Kg</th>
                        <th>Weight</th>
                        <th>Size</th>
                        <th>Rate</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="productList.length > 0" v-for="(product,index) in productList" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ product['description'] }}</td>
                        <td>{{ product['qty_by_pc'] }}</td>
                        <td>{{ product['qty_by_kg'] }}</td>
                        <td>{{ product['weight'] }}</td>
                        <td>{{ product['size'] }}</td>
                        <td>{{ product['rate'] }}</td>
                        <td>{{ product['order_price'] }}</td>
                        <td><button @click="removeProduct(index)" class="btn btn-danger">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <div>

                <label for ="deliveryDate">Delivery Date:
                <input v-model="form.delivery_date" type="date" class="form-control-sm h-10 border border-gray-300 rounded-md">
               </label>
               <label class="ml-4" for="deliveryPlace">Delivery Place:
                <input v-model="form.delivery_place" type="text" class="form-control-sm h-10 border border-gray-300 rounded-md">
               </label>
             <p>
                <label for="note">Note(Remark):</label>
                <textarea v-model="form.note" class="form-control-sm w-full h-24 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"  placeholder="Add note here"></textarea>
             </p>
            </div>

            <div class="mt-4">
                <p>Total Order By Pcs: {{ calculate.totalByPc?calculate.totalByPc:0 }}</p>
                <p>Total Order By Kg: {{ calculate.totalByKg?calculate.totalByKg.toFixed(2):0 }}</p>
            </div>



            <button @click="createInvoice()" class="btn btn-success">Confirm Order</button>
        </div>
    </div>
</template>

<style scoped>
.form-control-sm,
.form-select-sm,
.btn-sm {
    padding: 4px 8px;      /* Smaller padding */
    font-size: 11px;       /* Reduce font size */
    line-height: 18px;     /* Adjust line height */
    border-radius: 0px;    /* Smaller border radius */
}
/* Modal Styles */
.modal-dialog {
    max-width: 500px;
    margin-top: 150px;
    /* Reduce modal width */
}

.modal-header, .modal-body, .modal-footer {
    padding: 10px;
    background-color: #a9cbee; /* Reduce padding */
}

.modal-title {
    font-size: 28px; /* Smaller modal title font */
}

.form-control {
    font-size: 14px; /* Smaller input text */
    padding: 6px 10px; /* Reduce input padding */
}

/* Button Styles */
.btn {
    font-size: 14px; /* Reduce button font size */
    padding: 6px 12px; /* Reduce button padding */
}

.btn-close {
    padding: 0.5rem; /* Adjust button close size */
}

.table {
    font-size: 14px; /* Smaller table font size */
}

.table th, .table td {
    padding: 8px; /* Reduce padding inside table cells */
}

/* Table Header */
.table thead th {
    background-color: #f8f9fa; /* Subtle background for header */
    font-weight: 600;
}

/* Adjustments for EasyDataTable */
.easy-data-table {
    padding: 8px; /* Adjust overall padding */
}

.easy-data-table table td,
.easy-data-table table th {
    font-size: 14px; /* Smaller font for table content */
    padding: 6px 10px; /* Reduce padding for table cells */
}

/* Input Fields */
input.form-control {
    height: 28px; /* Reduce input height */
    font-size: 14px; /* Adjust input text size */
}

textarea.form-control {
    font-size: 14px; /* Adjust textarea text size */
}

/* Overall Container */
.container {
    max-width: 100%; /* Limit the container width */
}

h1 {
    font-size: 24px; /* Adjust header font size */
}

/* Invoice Section */
.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th, .table-bordered td {
    font-size: 14px; /* Reduce table font size */
    padding: 8px; /* Reduce table cell padding */
}

/* Reduce space for 'Add' buttons */
.btn-sm {
    padding: 4px 8px; /* Smaller padding for small buttons */
    font-size: 12px; /* Smaller font size for small buttons */
    border-radius: 0px;
}

/* Delivery date and place fields */
input[type="date"], input[type="text"] {
    height: 28px; /* Adjust input height */
    font-size: 14px; /* Smaller font size */
}

/* Invoice Totals */
.mt-4 p {
    font-size: 14px; /* Reduce font size for total labels */
}

</style>


