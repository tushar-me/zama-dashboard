<script setup lang="ts">
import { ref,computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { Category } from '@/types';
import UFileUpload from '@/components/UFileUpload.vue';
defineOptions({ layout: AppLayout });
const confirm = useConfirm();
const toast = useToast();
const props = defineProps<{
    errors: any,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Create Catergory' }
]);
const form = useForm({
    name: '',
    order_level: 0,
    image: null,
    type: 'mockup',
    print_type: 'default_print',
    status: 1,
    title: '',
    us_charge: 0,
    us_add_charge_per_item: 0,
    worldwide_charge: 0,
    worldwide_add_charge_per_item: 0,
})

const onSubmit = () => {
    form.post('/category', {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Category has been created successfully', life: 3000 });
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
    <Head title="Mockup Category" />
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
                <div>
                    <InputNumber v-model="form.order_level" id="order_level" class="flex-auto" autocomplete="off" />
                    <Message v-if="errors.order_level" severity="error" size="small" variant="simple">{{ errors.order_level }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4 ">
                <label for="type" class="font-normal lg:w-1/4">Type</label>
                <div class="w-full lg:w-3/4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.type" inputId="mockup" name="type" value="mockup" />
                            <label for="mockup">Mockup</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.type" inputId="ecommerce" name="type" value="ecommerce" />
                            <label for="ecommerce">Ecommerce</label>
                        </div>
                    </div>
                    <Message v-if="errors.type" severity="error" size="small" variant="simple">{{ errors.type }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="print_type" class="font-normal lg:w-1/4">Print Type</label>
                <div class="w-full lg:w-3/4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.print_type" inputId="default_print" name="print_type" value="default_print" />
                            <label for="print_type">Default Print</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.print_type" inputId="all_over_print" name="print_type" value="all_over_print" />
                            <label for="all_over_print">All Over Print</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.print_type" inputId="both_print" name="print_type" value="both_print" />
                            <label for="print_type">Both Print</label>
                        </div>
                    </div>
                    <Message v-if="errors.print_type" severity="error" size="small" variant="simple">{{ errors.print_type }}</Message>
                </div>
            </div>
            <div class="flex items-center gap-4 mb-2">
                <label for="image" class="font-semibold  lg:w-1/4">Image</label>
                <div>
                    <UFileUpload v-model="form.image" />
                    <span v-if="errors.image" class="text-red-500">{{ errors.image }}</span>
                </div>
            </div>
            <h3 class="text-xl font-normal mb-4">Shipping Information</h3>
            <div class="flex gap-4 mb-4">
                <label for="us_charge"  class="font-normal lg:w-1/4">US Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.us_charge" id="us_charge" :invalid="errors.us_charge" autocomplete="off" />
                    <Message v-if="errors.us_charge" severity="error" size="small" variant="simple">{{ errors.us_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="us_add_charge_per_item"  class="font-normal lg:w-1/4">US Add Charge Per Item</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.us_add_charge_per_item" id="us_add_charge_per_item" :invalid="errors.us_add_charge_per_item" autocomplete="off" />
                    <Message v-if="errors.us_add_charge_per_item" severity="error" size="small" variant="simple">{{ errors.us_add_charge_per_item }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="worldwide_charge"  class="font-normal lg:w-1/4">Worldwide Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.worldwide_charge" id="worldwide_charge" :invalid="errors.worldwide_charge" autocomplete="off" />
                    <Message v-if="errors.worldwide_charge" severity="error" size="small" variant="simple">{{ errors.worldwide_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="worldwide_add_charge_per_item"  class="font-normal lg:w-1/4">Worldwide Add Charge Per Item</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.worldwide_add_charge_per_item" id="worldwide_add_charge_per_item" :invalid="errors.worldwide_add_charge_per_item" autocomplete="off" />
                    <Message v-if="errors.worldwide_add_charge_per_item" severity="error" size="small" variant="simple">{{ errors.worldwide_add_charge_per_item }}</Message>
                </div>
            </div>
            <div  class="flex gap-4 mb-4">
                <div class=" lg:w-1/4"></div>
                <div class="flex items-center gap-2">
                    <Button class="w-40" label="Cancel" text severity="secondary" autofocus />
                    <Button class="w-60" @click="onSubmit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </div>
        </form>
    </div>
</template>
