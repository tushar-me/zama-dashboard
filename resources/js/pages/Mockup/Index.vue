<script setup lang="ts">
import { ref,computed, reactive } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { Mockup } from '@/types';
import { watchDebounced } from '@vueuse/core'


defineOptions({ layout: AppLayout });


const toast = useToast();
const props = defineProps<{
    mockups: {
        data: Mockup[],
        meta: {
            current_page: number,
            per_page: number,
            total: number,
        },
    },
    search: string,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Mockup' }
]);

const search = reactive<{ value: string }>({ value: props.search ?? '' });
watchDebounced(search, () => {
  getMockups()
}, { debounce: 500 })

const getMockups = () => {
  router.get(route("mockup.index"), { search: search.value }, {
    preserveState: true,
    preserveScroll: true,
  })
}
function onPageChange(event: any) {
  const currentPage = event.page + 1

  router.get(route("mockup.index"), {
    page: currentPage,
    limit: event.rows,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

//edit mockup side
const selectedMockups = ref([]);
const selectedMockup = ref<Mockup | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Manage Variation', icon: 'pi pi-pen-to-square', command: () => variation(selectedMockup.value?.id)},
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedMockup.value?.id)},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};
const edit = (id?: string) => {
    router.visit(route('mockup.edit', id));
}

const variation = (id?: string) => {
    router.visit(route('variation-price.index') + `?mockup=${id}`);
}

// Delete Mockup
const visibleDeleteDialog = ref(false);
function onDelete() {
    if (selectedMockup.value) {
        router.delete(route('mockup.destroy', selectedMockup.value.id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup has been deleted successfully', life: 3000 });
                const audio = new Audio('/success.wav');
                audio.play();
                visibleDeleteDialog.value = false;
                selectedMockup.value = null;
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
                const audio = new Audio('/error.wav');
                audio.play();
            },
        });
    }
}
</script>

<template>
    <Head title="Mockup Category" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="mockups?.data" v-model:selection="selectedMockups" contextMenu v-model:contextMenuSelection="selectedMockup"  @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText placeholder="Search" />
                        </IconField>
                        <Button asChild v-slot="slotProps">
                            <Link href="/mockup/create" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded"/>
                                Create Mockup
                            </Link>
                        </Button>
                    </div>
                </div>
                <Paginator
                        :rows="props.mockups.meta.per_page ?? 10"
                        :first="(props.mockups.meta.current_page - 1) * props.mockups.meta.per_page"
                        :totalRecords="props.mockups.meta.total"
                        :rowsPerPageOptions="[10, 20, 30]"
                        @page="onPageChange"
                    />
            </template>
            <template #empty> No mockup found. </template>
            <template #loading> Loading mockup data. Please wait. </template>
            <Column field="name" header="Name" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="image" header="Image" style="min-width: 8rem">
                <template #body="{ data }">
                    <img class="size-20 object object-contain" :src="data?.image" :alt="data?.name">
                </template>
            </Column>
             <Column field="created_by" header="Creator" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.creator?.name }}
                </template>
            </Column>
            <Column field="last_updated_at" header="Last Updated By" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.editor?.name }}
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    {{ data?.status }}
                </template>
            </Column>
            <Column field="type" header="Type">
                <template #body="{ data }">
                    <span v-if="data?.type == 'default'" class="uppercase text-sm bg-slate-300 p-2 rounded-md">{{ data?.type }}</span>
                    <span v-else class="uppercase text-sm bg-amber-300 p-2 rounded-md">{{ data?.type }}</span>
                </template>
            </Column>
             <Column field="order_level" header="Order Level">
                <template #body="{ data }">
                    {{ data?.order_level }}
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                    <div class="flex items-center">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="variation(data?.id)" />
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="edit(data?.id)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedMockup = data, visibleDeleteDialog=true" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
    <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedMockup = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedMockup?.name }} Country</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedMockup?.name }}</span> Country ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2  p-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete()" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
