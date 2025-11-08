<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { Color } from '@/types';
import { watchDebounced } from '@vueuse/core'


defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    colors: {
        data: Color[],
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
    { label: 'Color' }
]);


function onPageChange(event: any) {
  const currentPage = event.page + 1

  router.get(route("color.index"), {
    page: currentPage,
    limit: event.rows,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const search = ref<string | null>(props.search || "")
const searchInput = ref<HTMLInputElement | null>(null)

watchDebounced(search, () => {
  getColors()
}, { debounce: 500 })

const getColors = () => {
  router.get(route("color.index"), { search: search.value }, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Create Color
const createDialog = ref(false);
const form = useForm({
    name: '',
    hex_code: '',
    order_level: 0,
    status: true,
})

const onSubmit = () => {
    form.post('/color', {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Color has been created successfully', life: 3000 });
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

const selectedColors = ref([]);
const selectedColor = ref<Color | null>(null);
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
watch(() => selectedColor.value, (newVal) => {
    if (newVal) {
        form.name = newVal.name;
        form.hex_code = newVal.hex_code;
        form.order_level = newVal.order_level;
        form.status = newVal.status;
    }
})

const onUpdate = () => {
    router.put(`/color/${selectedColor.value?.id}`, {
        name: form.name,
        hex_code: form.hex_code,
        order_level: form.order_level,
        status: form.status,
    },
    {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Color has been updated successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleEditDialog.value = false;
            selectedColor.value = null;
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
    router.delete(`/color/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Color has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedColor.value = null;
        }
    })
}

</script>

<template>
    <Head title="Colors" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="colors?.data" v-model:selection="selectedColors" contextMenu v-model:contextMenuSelection="selectedColor"
        @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <form @submit.prevent="getColors">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="search" ref="searchInput" placeholder="Search" />
                            </IconField>
                        </form>
                        <Button  icon="pi pi-plus" @click="createDialog = true" label="Create Color" />
                    </div>
                </div>
                <Paginator
                        :rows="props.colors.meta.per_page ?? 10"
                        :first="(props.colors.meta.current_page - 1) * props.colors.meta.per_page"
                        :totalRecords="props.colors.meta.total"
                        :rowsPerPageOptions="[10, 20, 30]"
                        @page="onPageChange"
                    />
                <ul v-if="selectedColors.length > 0" class="flex items-center gap-3 px-4">
                    <li v-if="selectedColors.length == 1">
                        <Button icon="pi pi-pen-to-square" label="Edit" size="small" @click="deleteColors" />
                    </li>
                    <li>
                        <Button icon="pi pi-trash" label="Delete" size="small" severity="danger" @click="deleteColors" />
                    </li>
                </ul>
            </template>
            <template #empty> No Color found. </template>
            <template #loading> Loading category data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="color" header="Color" style="min-width: 8rem">
                <template #body="{ data }">
                    <span class="size-6  shadow block" :style="{ backgroundColor: '#' + data?.hex_code }"></span>
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
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="selectedColor = data, visibleEditDialog = true" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedColor = data, visibleDeleteDialog=true" />
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Create Color Dialog -->
     <Dialog v-model:visible="createDialog" modal header="Create Color" :style="{ width: '35rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Create Color</span>
                </div>
            </template>
            <form @submit.prevent="onSubmit" class="p-3">
                <div class="flex  gap-4 mb-4">
                    <label for="name" class="font-semibold lg:w-1/4">Name</label>
                    <div class="w-full lg:w-3/4">
                        <InputText v-model="form.name" id="name" autocomplete="off" :invalid="errors.name" class="w-full" />
                        <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-4">
                    <label for="type" class="font-semibold lg:w-1/4">Color</label>
                    <div class="w-full lg:w-3/4">
                        <ColorPicker inline v-model="form.hex_code" id="hex_code" class="w-full" autocomplete="off" />
                        <InputGroup >
                            <InputGroupAddon>
                                #
                            </InputGroupAddon>
                            <InputText  v-model="form.hex_code" id="hex_code" autocomplete="off" :invalid="errors.hex_code" />
                        </InputGroup>
                        <Message v-if="errors.hex_code" severity="error" size="small" variant="simple">{{ errors.hex_code }}</Message>
                    </div>
                </div>
                <div class="flex gap-4 mb-2">
                    <label for="order_level" class="font-semibold lg:w-1/4">Order Level</label>
                    <div class="w-full lg:w-3/4">
                        <InputNumber v-model="form.order_level" id="order_level" class="flex-auto w-full" autocomplete="off" :invalid="errors.order_level" />
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
    <!-- Create Category Dialog End -->

    <!-- Update Color Dialog -->
    <Dialog v-model:visible="visibleEditDialog" @hide="selectedColor = null" modal header="Edit Color" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Edit Color</span>
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
                <div class="flex gap-4 mb-4">
                    <label for="type" class="font-semibold lg:w-1/4">Color</label>
                    <div class="w-full lg:w-3/4">
                        <ColorPicker inline v-model="form.hex_code" id="hex_code" class="w-full" autocomplete="off" />
                        <InputGroup >
                            <InputGroupAddon>
                                #
                            </InputGroupAddon>
                            <InputText  v-model="form.hex_code" id="hex_code" autocomplete="off" :invalid="errors.hex_code" />
                        </InputGroup>
                        <Message v-if="errors.hex_code" severity="error" size="small" variant="simple">{{ errors.hex_code }}</Message>
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
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedColor = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedColor?.name }} Color</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedColor?.name }} Color</span> ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedColor?.id)" :disabled="form.processing" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
