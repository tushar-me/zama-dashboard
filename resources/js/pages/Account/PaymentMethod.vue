<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import { watchDebounced } from '@vueuse/core'
import UFileUpload from '@/components/UFileUpload.vue';


defineOptions({ layout: AppLayout });

type PayemntMethod = {
    id: string;
    name: string;
    logo: string;
    order_level: number;
    status: boolean;
    is_vendor_payment_method: boolean;
}
const toast = useToast();
const props = defineProps<{
    methods: {
        data: PayemntMethod[],
        meta: {
            per_page: number,
            current_page: number,
            total: number,
        },
    },
    current_page: number,
    errors: any,
    search: string,
    limit: number,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Payment Methods' }
]);


function onPageChange(event: any) {
  const currentPage = event.page + 1

  router.get(route("payment-method.index"), {
    page: currentPage,
    limit: event.rows,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const search = ref<string | null>(props.search || '');
const searchInput = ref<HTMLInputElement | null>(null);

watchDebounced(
  search,
  () => {
    getPaymentMethods();
  },
  { debounce: 500 }
);
const getPaymentMethods = () => {
    router.get(route('payment-method.index'), { search: search.value });
}

// Create Sizer
const createDialog = ref(false);
const form = useForm({
    name: '',
    logo: '',
    order_level: 0,
    status: true as boolean,
    is_vendor_payment_method: false as boolean,
})

const onSubmit = () => {
    form.post('/payment-method', {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Payment Method has been created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            createDialog.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        }
    })
}

const selectedMethods = ref([]);
const selectedMethod = ref<PayemntMethod | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => visibleEditDialog.value = true},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};
// Edit Record
const visibleEditDialog = ref(false);
watch(() => selectedMethod.value, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
        form.order_level = newVal.order_level;
        form.status = newVal.status;
        form.is_vendor_payment_method = newVal.is_vendor_payment_method;
    }
})

const onUpdate = () => {
    router.post(`/payment-method/${selectedMethod.value?.id}`, {
        _method: 'PUT',
        name: form.name,
        order_level: form.order_level,
        status: form.status,
        is_vendor_payment_method: form.is_vendor_payment_method,
        logo: form.logo,
    },
    {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Payment Method has been updated successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleEditDialog.value = false;
            selectedMethod.value = null;
            form.reset();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        }
    })
}

// Delete Record
const visibleDeleteDialog = ref(false);
const onDelete = (id: string | undefined) => {
    router.delete(`/payment-method/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Payment Method has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedMethod.value = null;
        }
    })
}

</script>

<template>
    <Head title="Size" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="methods?.data" v-model:selection="selectedMethods" contextMenu v-model:contextMenuSelection="selectedMethod"
        @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <form @submit.prevent="getPaymentMethods">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="search" ref="searchInput" placeholder="Search" />
                            </IconField>
                        </form>
                        <Button  icon="pi pi-plus" @click="createDialog = true" label="Create Payment Method" />
                    </div>
                </div>
                <Paginator
                        :rows="props.methods.meta.per_page ?? 10"
                        :first="(props.methods.meta.current_page - 1) * props.methods.meta.per_page"
                        :totalRecords="props.methods.meta.total"
                        :rowsPerPageOptions="[10, 20, 30]"
                        @page="onPageChange"
                    />
                <ul v-if="selectedMethods.length > 0" class="flex items-center gap-3 px-4">
                    <li v-if="selectedMethods.length == 1">
                        <Button icon="pi pi-pen-to-square" label="Edit" size="small" @click="deleteSizes" />
                    </li>
                    <li>
                        <Button icon="pi pi-trash" label="Delete" size="small" severity="danger" @click="deleteSize" />
                    </li>
                </ul>
            </template>
            <template #empty> No Payment Method found. </template>
            <template #loading> Loading size data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="logo" header="Logo" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    <img class="size-12 object-contain" :src="data?.logo" :alt="data?.name">
                </template>
            </Column>
             <Column field="created_by" header="Creator" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.creator?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.created_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="last_updated_at" header="Last Updated By" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.editor?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.updated_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    <ToggleSwitch v-model="data.status" />
                </template>
            </Column>
            <Column field="is_vendor_payment_method" header="Vendor Payment Method" style="min-width: 10rem">
                <template #body="{ data }">
                    <ToggleSwitch v-model="data.is_vendor_payment_method" />
                </template>
            </Column>
             <Column field="order_level" header="Order Level">
                <template #body="{ data }">
                    {{ data?.order_level }}
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="selectedMethod = data, visibleEditDialog = true" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedMethod = data, visibleDeleteDialog=true" />
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Create Method Dialog -->
     <Dialog v-model:visible="createDialog" modal header="Create Payment Method" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Create Payment Method</span>
                </div>
            </template>
            <form @submit.prevent="onSubmit" class="p-3">
                <div class="flex  gap-4 mb-4">
                    <label for="name" class="font-semibold lg:w-1/4">Name</label>
                    <div class="w-full lg:w-3/4">
                        <InputText v-model="form.name" id="name" autocomplete="off" :invalid="errors.name" />
                        <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                    </div>
                </div>
                <div class="flex  gap-4 mb-4">
                    <label for="logo" class="font-semibold lg:w-1/4">Logo</label>
                    <div class="w-full lg:w-3/4">
                        <UFileUpload v-model="form.logo" id="name"  />
                        <Message v-if="errors.logo" severity="error" size="small" variant="simple">{{ errors.logo }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="order_level" class="font-semibold lg:w-1/4">Order Level</label>
                    <div class="w-full lg:w-3/4">
                        <InputNumber v-model="form.order_level" id="order_level" class="flex-auto" autocomplete="off" :invalid="errors.order_level" />
                        <Message v-if="errors.order_level" severity="error" size="small" variant="simple">{{ errors.order_level }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="status" class="font-semibold lg:w-1/4">Status</label>
                    <div class="w-full lg:w-3/4">
                        <ToggleSwitch v-model="form.status" :invalid="errors.status"/>
                        <Message v-if="errors.status" severity="error" size="small" variant="simple">{{ errors.status }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="status" class="font-semibold lg:w-1/4">Vendor Payment Method</label>
                    <div class="w-full lg:w-3/4">
                        <ToggleSwitch v-model="form.is_vendor_payment_method" :invalid="errors.is_vendor_payment_method"/>
                        <Message v-if="errors.is_vendor_payment_method" severity="error" size="small" variant="simple">{{ errors.is_vendor_payment_method }}</Message>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" text severity="secondary" @click="createDialog = false" autofocus />
                    <Button type="submit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </form>
        </Dialog>
    <!-- Create Size Dialog End -->

    <!-- Update Dialog -->
    <Dialog v-model:visible="visibleEditDialog" @hide="selectedMethod = null" modal header="Edit Payment Method" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Edit Payment Method</span>
                </div>
            </template>
            <form @submit.prevent="onUpdate" class="p-3">
                <div class="flex  gap-4 mb-4">
                    <label for="name" class="font-semibold lg:w-1/4">Name</label>
                    <div class="w-full lg:w-3/4">
                        <InputText v-model="form.name" id="name" autocomplete="off" :invalid="errors.name" />
                        <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                    </div>
                </div>
                <div class="flex  gap-4 mb-4">
                    <label for="logo" class="font-semibold lg:w-1/4">Logo</label>
                    <div class="w-full lg:w-3/4">
                        <UFileUpload v-model="form.logo" :initialImage="selectedMethod?.logo" id="logo" />
                        <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.logo }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="order_level" class="font-semibold lg:w-1/4">Order Level</label>
                    <div class="w-full lg:w-3/4">
                        <InputNumber v-model="form.order_level" id="order_level" class="flex-auto" autocomplete="off" :invalid="errors.order_level" />
                        <Message v-if="errors.order_level" severity="error" size="small" variant="simple">{{ errors.order_level }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="status" class="font-semibold lg:w-1/4">Status</label>
                    <div class="w-full lg:w-3/4">
                        <ToggleSwitch v-model="form.status" :invalid="errors.status"/>
                        <Message v-if="errors.status" severity="error" size="small" variant="simple">{{ errors.status }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="status" class="font-semibold lg:w-1/4">Vendor Payment Method</label>
                    <div class="w-full lg:w-3/4">
                        <ToggleSwitch v-model="form.is_vendor_payment_method" :invalid="errors.is_vendor_payment_method"/>
                        <Message v-if="errors.is_vendor_payment_method" severity="error" size="small" variant="simple">{{ errors.is_vendor_payment_method }}</Message>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" text severity="secondary" @click="visibleEditDialog = false" autofocus />
                    <Button type="submit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </form>
        </Dialog>
    <!-- Update Dialog End -->

     <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedMethod = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedMethod?.name }} Payment Method</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedMethod?.name }} Payment Method</span> ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedMethod?.id)" :disabled="form.processing" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>