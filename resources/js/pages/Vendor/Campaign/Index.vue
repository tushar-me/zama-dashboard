<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { Campaign } from '@/types';
import { watchDebounced } from '@vueuse/core'

defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    campaigns: {
        data: Campaign[],
        total: number,
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
    { label: 'Vendor'},
    { label: 'Store' },
    { label: 'Campaign' },
]);

const rows = ref(10) 
const first = ref(props.current_page)

function onPageChange(event: any) {
    const currentPage = event.page + 1 
    rows.value = event.rows
    first.value = event.first
    router.get(route('category.index'), {
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
    getCampaigns();
  },
  { debounce: 500 }
);
const getCampaigns = () => {
    router.get(route('campaign.index'), { search: search.value, limit: limit.value, page: page.value });
}


const selectedCampaigns = ref([]);
const selectedCampaign = ref<Campaign | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedCampaign.value?.id)},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};

const edit = (id?: string) => {
    router.visit(route('campaign.edit', id));
}
// Delete Record
const visibleDeleteDialog = ref(false);
const onDelete = (id: string | undefined) => {
    router.delete(`/campaign/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Campaign has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedCampaign.value = null;
        }
    })
}

</script>

<template>
    <Head title="Countries" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="campaigns?.data" v-model:selection="selectedCampaigns" contextMenu v-model:contextMenuSelection="selectedCampaign"
        @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <form @submit.prevent="getCampaigns">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="search" ref="searchInput" placeholder="Search" />
                            </IconField>
                        </form>
                    </div>
                </div>
                <Paginator
                    :rows="rows"
                    :totalRecords="stores?.total"
                    :rowsPerPageOptions="[10, 20, 30]"
                    @page="onPageChange"
                >
                    <template #start="slotProps">
                        Page: {{ slotProps.state.page + 1 }}
                        First: {{ slotProps.state.first }}
                        Rows: {{ slotProps.state.rows }}
                    </template>
                </Paginator>
                <ul v-if="selectedCampaigns.length > 0" class="flex items-center gap-3 px-4">
                    <li v-if="selectedCampaigns.length == 1">
                        <Button icon="pi pi-pen-to-square" label="Edit" size="small" @click="deleteCampaigns" />
                    </li>
                    <li>
                        <Button icon="pi pi-trash" label="Delete" size="small" severity="danger" @click="deleteCampaigns" />
                    </li>
                </ul>
            </template>
            <template #empty> No Color found. </template>
            <template #loading> Loading Campaign data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="image" header="Image" style="min-width: 8rem">
                <template #body="{ data }">
                    <img :src="data?.image" :alt="data?.name" class="w-16 h-auto">
                </template>
            </Column>
            <Column field="store" header="Store" style="min-width: 8rem">
                <template #body="{ data }">
                    <span class="text-sm font-normal block">{{ data?.store?.name }}</span>
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
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedCampaign = data, visibleDeleteDialog=true" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
     <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedCampaign = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedCampaign?.name }} Country</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedCampaign?.name }}</span> Campaign ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2  p-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedCampaign?.id)" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
