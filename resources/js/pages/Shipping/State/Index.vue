<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { State } from '@/types';
import { watchDebounced } from '@vueuse/core'

defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    states: {
        data: State[],
        meta: {
            per_page: number,
            current_page: number,
            from: number,
        },
    },
    search: string,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'State' }
]);

function onPageChange(event: any) {
  const currentPage = event.page + 1

  router.get(route("state.index"), {
    page: currentPage,
    limit: event.rows,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const search = ref<string | null>(props.search || '');
const limit = ref(10);
const page = ref(1);
const searchInput = ref<HTMLInputElement | null>(null);

watchDebounced(
  search,
  () => {
    getStates();
  },
  { debounce: 500 }
);
const getStates = () => {
    router.get(route('state.index'), { search: search.value, limit: limit.value, page: page.value });
}


const selectedStates = ref([]);
const selectedState = ref<State | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedState.value?.id)},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};

const edit = (id: string) => {
    router.visit(route('state.edit', id));
}
// Delete Record
const visibleDeleteDialog = ref(false);
const onDelete = (id: string | undefined) => {
    router.delete(`/state/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'State has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedState.value = null;
        }
    })
}

</script>

<template>
    <Head title="States" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="states?.data" v-model:selection="selectedStates" contextMenu v-model:contextMenuSelection="selectedState"
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
                            <Link href="/state/create" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded"/>
                                Create State
                            </Link>
                        </Button>
                        <Button asChild v-slot="slotProps">
                            <Link href="/state/bulk-create" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded"/>
                                Bulk Create
                            </Link>
                        </Button>
                    </div>
                </div>
                <Paginator
                    :rows="props.states.meta.per_page ?? 10"
                    :first="(props.states.meta.current_page - 1) * props.states.meta.per_page"
                    :totalRecords="props.states.meta.total"
                    :rowsPerPageOptions="[50, 100, 200]"
                    @page="onPageChange"
                />
                <ul v-if="selectedStates.length > 0" class="flex items-center gap-3 px-4">
                    <li v-if="selectedStates.length == 1">
                        <Button icon="pi pi-pen-to-square" label="Edit" size="small" @click="deleteStates" />
                    </li>
                    <li>
                        <Button icon="pi pi-trash" label="Delete" size="small" severity="danger" @click="deleteStates" />
                    </li>
                </ul>
            </template>
            <template #empty> No Color found. </template>
            <template #loading> Loading state data. Please wait. </template>
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
            <Column field="code" header="Code" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.code }}
                </template>
            </Column>
            <Column field="iso_code" header="ISO code" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.iso_code }}
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
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    <ToggleSwitch v-model="data.status" />
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                    <div class="flex items-center">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="edit(data?.id)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedState = data, visibleDeleteDialog=true" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
     <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedState = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedState?.name }} State</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedState?.name }}</span> State ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2  p-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedState?.id)" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
