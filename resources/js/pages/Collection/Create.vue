<script setup lang="ts">
import { ref,computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { Category, Product } from '@/types';
import UFileUpload from '@/components/UFileUpload.vue';
defineOptions({ layout: AppLayout });
const confirm = useConfirm();
const toast = useToast();
const props = defineProps<{
    errors: any,
    products: Product[]
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Create Collection' }
]);
const form = useForm({
    name: '',
    order_level: 0,
    image: null,
    status: 1,
    product_ids: [],
    title: '',
})

const onSubmit = () => {
    form.post('/collection', {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Collection has been created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            form.reset();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        }
    })
}
</script>
<template>
    <Head title="Create Collection" />
    <Toast />

    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
         <Breadcrumb :home="home" :model="items" />
        <form @submit.prevent="onSubmit" class="p-4">
            <div class="flex items-center gap-4 mb-4">
                <label for="name" class="font-semibold  lg:w-1/4">Name</label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.name" id="name" class="flex-auto" autocomplete="off" :invalid="errors.name"/>
                    <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="order_level" class="font-semibold lg:w-1/4">Order Level</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber v-model="form.order_level" id="order_level" class="flex-auto" autocomplete="off" />
                    <Message v-if="errors.order_level" severity="error" size="small" variant="simple">{{ errors.order_level }}</Message>
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="image" class="font-semibold  lg:w-1/4">Image</label>
                <div class="w-full lg:w-3/4">
                    <UFileUpload v-model="form.image" />
                    <span v-if="errors.image" class="text-red-500">{{ errors.image }}</span>
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="image" class="font-semibold  lg:w-1/4">Products</label>
                <div class="w-full lg:w-3/4">
                    <MultiSelect class="w-full" v-model="form.product_ids"  :options="products" filter  option-value="id" optionLabel="name" placeholder="Select Products" />
                    <span v-if="errors.image" class="text-red-500">{{ errors.product_ids }}</span>
                </div>
            </div>
            <div  class="flex gap-4 mb-4">
                <div class=" lg:w-1/4"></div>
                <div class="flex items-center gap-2">
                    <Button class="w-60" @click="onSubmit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </div>
        </form>
    </div>
</template>
