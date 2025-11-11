<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { Size } from '@/types';
import { watchDebounced } from '@vueuse/core'


defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    sizes: {
        data: Size[],
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
    { label: 'Size' }
]);


function onPageChange(event: any) {
  const currentPage = event.page + 1

  router.get(route("country.index"), {
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
    getSizes();
  },
  { debounce: 500 }
);
const getSizes = () => {
    router.get(route('size.index'), { search: search.value });
}

// Create Sizer
const createDialog = ref(false);
const form = useForm({
    name: '',
    order_level: 0,
    status: true,
})

const onSubmit = () => {
    form.post('/size', {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Size has been created successfully', life: 3000 });
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

const selectedSizes = ref([]);
const selectedSize = ref<Size | null>(null);
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
watch(() => selectedSize.value, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
        form.order_level = newVal.order_level;
        form.status = newVal.status;
    }
})

const onUpdate = () => {
    router.put(`/size/${selectedSize.value?.id}`, {
        name: form.name,
        order_level: form.order_level,
        status: form.status,
    },
    {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Size has been updated successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleEditDialog.value = false;
            selectedSize.value = null;
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
    router.delete(`/size/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Size has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedSize.value = null;
        }
    })
}

</script>

<template>
    <Head title="Size" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="sizes?.data" v-model:selection="selectedSizes" contextMenu v-model:contextMenuSelection="selectedSize"
        @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <form @submit.prevent="getSizes">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="search" ref="searchInput" placeholder="Search" />
                            </IconField>
                        </form>
                        <Button  icon="pi pi-plus" @click="createDialog = true" label="Create Size" />
                    </div>
                </div>
            </template>
            <template #empty> No Size found. </template>
            <template #loading> Loading size data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    {{ data?.name }}
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
             <Column field="order_level" header="Order Level">
                <template #body="{ data }">
                    {{ data?.order_level }}
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="selectedSize = data, visibleEditDialog = true" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedSize = data, visibleDeleteDialog=true" />
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Create Category Dialog -->
     <Dialog v-model:visible="createDialog" modal header="Create Size" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Create Size</span>
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
                        <!-- <ToggleSwitch v-model="toggleStatus" /> -->
                        <Message v-if="errors.status" severity="error" size="small" variant="simple">{{ errors.status }}</Message>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" text severity="secondary" @click="createDialog = false" autofocus />
                    <Button type="submit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </form>
        </Dialog>
    <!-- Create Size Dialog End -->

    <!-- Delete Dialog -->
    <Dialog v-model:visible="visibleEditDialog" @hide="selectedSize = null" modal header="Edit Size" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Edit Size</span>
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
                        <!-- <ToggleSwitch v-model="toggleStatus" /> -->
                        <Message v-if="errors.status" severity="error" size="small" variant="simple">{{ errors.status }}</Message>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" text severity="secondary" @click="visibleEditDialog = false" autofocus />
                    <Button type="submit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </form>
        </Dialog>
    <!-- Create Color Dialog End -->

     <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedSize = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedSize?.name }} Size</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedSize?.name }} Size</span> ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedSize?.id)" :disabled="form.processing" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
