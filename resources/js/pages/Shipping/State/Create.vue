<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { ref } from 'vue';
import type { Country } from '@/types';
defineOptions({ layout: AppLayout });

const props = defineProps<{
    errors: any,
    countries: {
        data: Country[]
    },
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'State' },
    { label: 'Create' }
]);

const toast = useToast();

const form = useForm({
    name: '',
    country_id: '',
    shipping_charge: 0,
    tax_percentage: 0,
    vat_percentage: 0,
    capital:'',
    is_default: false,
    iso_code: '',
    order_level: 0,
    status: true,
    timezone: '',
})

const onSubmit = () => {
    form.post(route('state.store'), {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Country has been created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        },
    });
}
</script>  
<template>
    <Head title="Create Country" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <Breadcrumb :home="home" :model="items" />
        <div class="p-10 pt-5">
            <form @submit.prevent="onSubmit">
            <div class="flex gap-4 mb-4">
                <label for="country_id" class="font-normal w-1/4">Country <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <Select v-model="form.country_id" :options="countries.data" filter optionValue="id" optionLabel="name" placeholder="Select a Country" />
                    <Message v-if="errors.country_id" severity="error" size="small" variant="simple">{{ errors.country_id }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="name"  class="font-normal w-1/4">Name <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.name" id="name" :invalid="errors.name" autocomplete="off" />
                    <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="iso_code" class="font-normal w-1/4">ISO code</label>
                 <div class="w-full lg:w-3/4">
                    <InputText v-model="form.iso_code" id="iso_code" :invalid="errors.iso_code" autocomplete="off" />
                    <Message v-if="errors.iso_code" severity="error" size="small" variant="simple">{{ errors.iso_code }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="shipping_charge" class="font-normal w-1/4">Shipping Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.shipping_charge" id="shipping_charge" :invalid="errors.shipping_charge" autocomplete="off" />
                    <Message v-if="errors.shipping_charge" severity="error" size="small" variant="simple">{{ errors.shipping_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="tax_percentage" class="font-normal w-1/4">Tax Percentage</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber v-model="form.tax_percentage" id="tax_percentage" :invalid="errors.tax_percentage" autocomplete="off" />
                    <Message v-if="errors.tax_percentage" severity="error" size="small" variant="simple">{{ errors.tax_percentage }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="vat_percentage" class="font-normal w-1/4">VAT Percentage</label>
                 <div class="w-full lg:w-3/4">
                    <InputNumber v-model="form.vat_percentage" id="vat_percentage" :invalid="errors.vat_percentage" autocomplete="off" />
                    <Message v-if="errors.vat_percentage" severity="error" size="small" variant="simple">{{ errors.vat_percentage }}</Message>
                </div>
            </div>

            <div>
                <Button type="submit" label="Save" icon="pi pi-save" class="w-80" block />
            </div>
           
            </form>
        </div>
    </div>
</template>
