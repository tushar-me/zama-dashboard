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
    categories: {
        data: Category[],
        meta: {
            per_page: number,
            current_page: number,
            from: number,
            total: number,
        },
    },
    current_page: number,
    errors: any,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Mockup Catergory' }
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
const selectedCategory = ref<Category | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedCategory.value?.id)},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};
const edit = (id?: string) => {
    router.visit(route('category.edit', id));
}
// Delete Record
const visibleDeleteDialog = ref(false);
const onDelete = (id: string | undefined) => {
    router.delete(`/category/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Category has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedCategory.value = null;
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

    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="categories?.data" contextMenu v-model:contextMenuSelection="selectedCategory"  @rowContextmenu="onRowContextMenu">
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
                            <Link href="/category/create" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded"/>
                                Create Category
                            </Link>
                        </Button>
                    </div>
                </div>
               <div class="flex items-center justify-between">
                <p>Showing {{props.categories.meta.from}} ot {{props.categories.meta.to}} of {{ props.categories.meta.total }}</p>
                <Paginator
                    :rows="props.categories.meta.per_page ?? 50"
                    :first="(props.categories.meta.current_page - 1) * props.categories.meta.per_page"
                    :totalRecords="props.categories.meta.total"
                    :rowsPerPageOptions="[50, 100, 200]"
                    @page="onPageChange"
                />
               </div>
            </template>
            <template #empty> No category found. </template>
            <template #loading> Loading category data. Please wait. </template>
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
            <Column field="email" header="Mockup Count" style="min-width: 6rem">
                <template #body="{ data }">
                    {{ data?.email }}
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
             <Column field="order_level" header="Order Level">
                <template #body="{ data }">
                    {{ data?.order_level }}
                </template>
            </Column>
            <Column style="min-width: 10rem">
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="edit(data?.id)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedCategory = data, visibleDeleteDialog=true" />
                </template>
            </Column>
        </DataTable>
    </div>
      <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedCategory = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedCategory?.name }} Category</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedCategory?.name }} Category</span> ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete(selectedCategory?.id)" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
