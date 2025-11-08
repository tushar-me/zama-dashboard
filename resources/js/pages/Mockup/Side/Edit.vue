<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import {ref, computed, watch} from 'vue';
import { MockupSide, Mockup } from '@/types';
import { FileUploadSelectEvent } from 'primevue/fileupload';
import DraggableResizableVue from 'draggable-resizable-vue3';

defineOptions({ layout: AppLayout });

const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home',

});
const items = ref([
    { label: 'Mockup', to: '/mockup'},
    { label: 'Side', to: '/mockup-side'},
    { label: 'Edit'}
]);
const props = defineProps<{
    mockupSide: {
        data: MockupSide
    },
    errors: any,
}>()

const toast = useToast();
const onFileSelect = (event: FileUploadSelectEvent) => {
    const file = event.files[0];
    form.image = file;
    form.peviewImg = URL.createObjectURL(file);
}
const onClear = () => {
    form.image = null;
    form.peviewImg = '';
}
const form = useForm({
    _method: 'put',
    mockup_id: props.mockupSide.data?.mockup_id,
    name: props.mockupSide.data?.name,
    image: null, 
    peviewImg: props.mockupSide.data?.image, 
    order_level: props.mockupSide.data?.order_level,
    bounding_box: {
        top: props.mockupSide.data?.bounding_box.top * 500,
        left: props.mockupSide.data?.bounding_box.left * 500,
        width: props.mockupSide.data?.bounding_box.width * 500,
        height: props.mockupSide.data?.bounding_box.height * 500,
        css: props.mockupSide.data?.bounding_box.css,
        border_color: props.mockupSide.data?.bounding_box.border_color,
        border_width: props.mockupSide.data?.bounding_box.border_width,
        background_color: props.mockupSide.data?.bounding_box.background_color,
    },
    status: props.mockupSide.data?.status,
})

const onSubmit = () => {
    form.post(route('mockup-side.update', props.mockupSide.data?.id), {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup side has been updated successfully', life: 3000 });
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
    <Head title="Edit Mockup Side" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <Breadcrumb :home="home" :model="items" />
        <div class="p-10 pt-5">
            <form @submit.prevent="onSubmit">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-normal">Mockup Side Information</h3>
                    <Button :disabled="form.processing" type="submit" label="Update Changes" icon="pi pi-save" />
                </div>
                <div class="mb-4 flex flex-wrap border-b border-gray-300 pb-5">
                    <div class="w-full lg:w-1/2">
                        <div class="flex gap-4 mb-4">
                            <label for="name"  class="font-normal lg:w-1/4">Name <span class="text-red-500">*</span></label>
                            <div class="w-full lg:w-3/4">
                                <InputText v-model="form.name" id="name" :invalid="errors.name" autocomplete="off" />
                                <Message
                                    v-if="errors.name"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    >
                                    {{ errors.name }}
                                </Message>
                            </div>
                        </div>
                        <div class="flex gap-4 mb-2">
                            <label for="image" class="font-normal lg:w-1/4">Image</label>
                            <div class="w-full lg:w-3/4 flex gap-5">
                                <div>
                                    <FileUpload :multiple="false" chooseIcon="pi pi-cloud-upload" :showUploadButton="false" @clear="onClear" :auto="true" @select="onFileSelect"  accept="image/*" :maxFileSize="1000000">
                                        <template #content>
                                            <div
                                            v-if="form.peviewImg"
                                            id="parent"
                                            class="relative size-[500px]"
                                                :style="
                                                    {
                                                        backgroundImage: `url(${form.peviewImg})`,
                                                        backgroundSize: 'cover',
                                                        backgroundPosition: 'center',
                                                        backgroundRepeat: 'no-repeat',
                                                        backgroundColor: `#${form.bounding_box.background_color}`,
                                                    }
                                                "
                                            >
                                                <DraggableResizableVue
                                                v-model:x="form.bounding_box.left"
                                                v-model:y="form.bounding_box.top"
                                                v-model:h="form.bounding_box.height"
                                                v-model:w="form.bounding_box.width"
                                                class="p-1 cursor-move" parent="#parent">
                                                    <span class="block w-full h-full" :style="
                                                        {
                                                            border: `${form.bounding_box.border_width}px solid #${form.bounding_box.border_color}`,
                                                        }
                                                    "></span>
                                                </DraggableResizableVue>
                                            </div>
                                            <div v-else class="flex flex-col items-center justify-center size-96 border border-gray-300 rounded-lg">
                                                <Icon name="line-md:uploading-loop" class="text-5xl text-gray-400" />
                                                <span class="text-gray-500">Drag and drop files to here to upload.</span>
                                            </div>
                                        </template>
                                    </FileUpload>
                                    <Message
                                        v-if="errors.image"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                        >
                                        {{ errors.image }}
                                    </Message>
                                    <div v-if="form.peviewImg">
                                        <h3 class="text-sm font-normal py-2">Background</h3>
                                        <ul class="flex flex-wrap">
                                            <li v-for="color in mockupSide?.data?.mockup?.colors">
                                                <input class="peer hidden" name="background-color" :id="`background-color-${color.id}`" :value="color.hex_code" type="radio" v-model="form.bounding_box.background_color" />
                                                <label :for="`background-color-${color?.id}`" class="block border border-transparent peer-checked:border-black p-0.5 rounded-lg">
                                                    <span :style="{ backgroundColor: '#'+color.hex_code }" class="size-6 rounded-md block"></span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5 border border-gray-200 rounded-lg p-4 bg-stone-100" v-if="form.peviewImg">
                                    <div class="space-y-4">
                                        <label for="bounding_box_top" class="font-normal text-sm">Top</label>
                                        <InputNumber showButtons  v-model="form.bounding_box.top" id="bounding_box_top" :invalid="errors.bounding_box_top" autocomplete="off" />
                                        <Slider v-model="form.bounding_box.top" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_left" class="font-normal text-sm">Left</label>
                                        <InputNumber showButtons  v-model="form.bounding_box.left" id="bounding_box_left" :invalid="errors.bounding_box_left" autocomplete="off" />
                                        <Slider v-model="form.bounding_box.left" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_width" class="font-normal text-sm">Width</label>
                                        <InputNumber showButtons  v-model="form.bounding_box.width" id="bounding_box_width" :invalid="errors.bounding_box_width" autocomplete="off" />
                                        <Slider v-model="form.bounding_box.width" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_height" class="font-normal text-sm">Height</label>
                                        <InputNumber showButtons  v-model="form.bounding_box.height" id="bounding_box_height" :invalid="errors.bounding_box_height" autocomplete="off" />
                                        <Slider v-model="form.bounding_box.height" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_border_color" class="font-normal text-sm">Border Color</label>
                                        <ColorPicker inline  v-model="form.bounding_box.border_color" id="bounding_box_border_color" :invalid="errors.bounding_box_border_color" autocomplete="off" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_border_width" class="font-normal text-sm">Border Width</label>
                                        <InputNumber :min="1" :max="5" showButtons  v-model="form.bounding_box.border_width" id="bounding_box_border_width" :invalid="errors.bounding_box_border_width" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
