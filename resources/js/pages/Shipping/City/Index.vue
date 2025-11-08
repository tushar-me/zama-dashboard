<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { City, State } from '@/types';
import { watchDebounced } from '@vueuse/core'

defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    cities: {
        data: City[],
        meta: {
            per_page: number,
            current_page: number,
            from: number,
        },
    },
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'City' }
]);

const rows = ref(10) 
const first = ref(props.current_page)

function onPageChange(event: any) {
    const currentPage = event.page + 1 
    rows.value = event.rows
    first.value = event.first
    router.get(route('city.index'), {
        page: currentPage,
        per_page: event.rows,
    })
}

const search = ref<string | null>(props.search || '');
const limit = ref(10);
const page = ref(1);
const searchInput = ref<HTMLInputElement | null>(null);

watchDebounced(
  search,
  () => {
    getCities();
  },
  { debounce: 500 }
);
const getCities = () => {
    router.get(route('city.index'), { search: search.value, limit: limit.value, page: page.value });
}


const selectedCties = ref([]);
const selectedCity = ref<City|null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedCity.value?.id)},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};

const edit = (id: string | undefined) => {
    router.visit(route('city.edit', id));
}
// Delete Record
const visibleDeleteDialog = ref(false);
const onDelete = (id: string | undefined) => {
    router.delete(`/city/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'City has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedCity.value = null;
        }
    })
}

</script>

<template>
    <Head title="City" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="cities?.data" v-model:selection="selectedCties" contextMenu v-model:contextMenuSelection="selectedCity"
        @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <form @submit.prevent="getStates">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="search" ref="searchInput" placeholder="Search" />
                            </IconField>
                        </form>
                        <Button asChild v-slot="slotProps">
                            <Link href="/city/create" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded"/>
                                Create City
                            </Link>
                        </Button>
                         <Button asChild v-slot="slotProps">
                            <Link href="/city/bulk-create" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded"/>
                                Bulk Create
                            </Link>
                        </Button>
                    </div>
                </div>
                <Paginator
                    :rows="props.cities?.meta?.per_page ?? 50"
                    :first="(props.cities.meta.current_page - 1) * props.cities.meta.per_page"
                    :totalRecords="props.cities.meta.total"
                    :rowsPerPageOptions="[50, 100, 200]"
                    @page="onPageChange"
                />
                <ul v-if="selectedCties.length > 0" class="flex items-center gap-3 px-4">
                    <li v-if="selectedCties.length == 1">
                        <Button icon="pi pi-pen-to-square" label="Edit" size="small" @click="deleteCity" />
                    </li>
                    <li>
                        <Button icon="pi pi-trash" label="Delete" size="small" severity="danger" @click="deleteCity" />
                    </li>
                </ul>
            </template>
            <template #empty> No City found. </template>
            <template #loading> Loading city data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="country" header="Country" style="min-width: 8rem">
                <template #body="{ data }">
                    <span class="text-sm font-normal">{{ data?.country?.name }}</span>
                </template>
            </Column>
            <Column field="state" header="State" style="min-width: 8rem">
                <template #body="{ data }">
                    <span class="text-sm font-normal">{{ data?.state?.name }}</span>
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
            <Column field="shipping_charge" header="Shipping Charge" style="min-width: 10rem">
                <template #body="{ data }">
                    ${{ data?.shipping_charge }}
                </template>
            </Column>
            <Column field="tax_percentage" header="Tax Percentage" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.tax_percentage }}%
                </template>
            </Column>
            <Column field="vat_percentage" header="Vat Percentage" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.vat_percentage }}%
                </template>
            </Column>
            <Column field="code" header="Code" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.code }}
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    <ToggleSwitch v-model="data.status" />
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                    <div class="flex items-center">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="edit(data?.id)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedCity = data, visibleDeleteDialog=true" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
     <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedCity = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedCity?.name }} City</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedCity?.name }}</span> City ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2  p-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedCity?.id)" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
