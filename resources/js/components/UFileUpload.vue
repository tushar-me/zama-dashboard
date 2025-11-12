<template>
  <div
    class="relative flex flex-col items-center justify-center min-h-[180px] p-6 border-1 border-gray-300 bg-slate-50 rounded-xl cursor-pointer transition-all duration-300 ease-in-out group"
    :class="{ 'border-primary-500 bg-primary-50 shadow-lg': isDragging, 'hover:border-primary-400 hover:bg-gray-50': !isDragging }"
    @dragover.prevent="onDragOver"
    @dragleave.prevent="onDragLeave"
    @drop.prevent="onDrop"
    @click="openFilePicker"
  >
    <input
      ref="fileInput"
      type="file"
      :accept="accept"
      :multiple="multiple"
      class="hidden"
      @change="onFileChange"
    />

    <div v-if="!hasFiles" class="flex flex-col items-center text-gray-900 transition-all duration-300 ease-in-out group-hover:text-primary-600">
      <span class="size-12 bg-primary flex items-center justify-center rounded-full">
        <Icon name="ic:outline-cloud-upload" class="text-2xl text-white" />
      </span>
      <p class="text-lg font-semibold">Drag & Drop your files here</p>
      <p class="text-sm mt-1">or <span class="text-primary-500 font-medium group-hover:underline">click to browse</span></p>
      <p v-if="accept" class="text-xs mt-2 text-gray-400">Accepted types: {{ accept }}</p>
    </div>

    <div v-else class="flex flex-wrap justify-center gap-3 mt-2 w-full">
      <div
        v-if="currentInitialImage && internalFiles.length === 0"
        :key="currentInitialImage.name"
        class="flex items-center p-3 border border-gray-200 rounded-lg bg-white shadow-md relative group-hover:shadow-lg transition-all duration-200 ease-in-out"
      >

        <img :src="currentInitialImage.preview" :alt="currentInitialImage.name" class="size-24 object-cover rounded-md mr-3" />
        <span class="text-sm text-gray-800 font-medium break-all max-w-[120px]">{{ currentInitialImage.name }}</span>
        <button
          @click.stop="removeInitialImage"
          class="ml-3 p-1 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div
        v-for="(file, index) in internalFiles"
        :key="file.name"
        class="flex items-center p-3 border border-gray-200 rounded-lg bg-white shadow-md relative group-hover:shadow-lg transition-all duration-200 ease-in-out"
      >
        <img v-if="isImage(file)" :src="file.preview" :alt="file.name" class="size-24 object-cover rounded-md mr-3" />
        <div v-else class="w-14 h-14 flex items-center justify-center bg-gray-100 rounded-md mr-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-7 h-7 text-gray-900"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
            />
          </svg>
        </div>
        <span class="text-sm text-gray-800 font-medium break-all max-w-[120px]">{{ file.name }}</span>
        <button
          @click.stop="removeFile(index)"
          class="ml-3 p-1 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface CustomFile extends File {
  preview?: string;
  isInitial?: boolean; 
}

interface Props {
  modelValue: File | File[] | string | null;
  accept: string;
  multiple: boolean;
  initialImage?: string | null; 
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: null,
  accept: 'image/*',
  multiple: false,
  initialImage: null,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: File | File[] | null): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref<boolean>(false);
const internalFiles = ref<CustomFile[]>([]);
const currentInitialImage = ref<CustomFile | null>(null); 

const hasFiles = computed<boolean>(() => internalFiles.value.length > 0 || currentInitialImage.value !== null);

watch(
  () => props.modelValue,
  (newValue) => {
    if (newValue === null || newValue === '' || (Array.isArray(newValue) && newValue.length === 0)) {
      internalFiles.value = [];
    } else if (newValue instanceof File) {
      internalFiles.value = [processFile(newValue)];
    } else if (Array.isArray(newValue)) {
      internalFiles.value = newValue.map(processFile);
    }

    // If modelValue is null/empty string/empty array and initialImage is present, display initialImage
    if ((newValue === null || newValue === '' || (Array.isArray(newValue) && newValue.length === 0)) && props.initialImage) {
      currentInitialImage.value = {
        name: props.initialImage.substring(props.initialImage.lastIndexOf('/') + 1),
        preview: props.initialImage,
        isInitial: true,
      } as CustomFile;
    } else {
      currentInitialImage.value = null;
    }
  },
  { immediate: true }
);

watch(
  () => props.initialImage,
  (newInitialImage) => {
    if (newInitialImage && (props.modelValue === null || props.modelValue === '' || (Array.isArray(props.modelValue) && props.modelValue.length === 0))) {
      currentInitialImage.value = {
        name: newInitialImage.substring(newInitialImage.lastIndexOf('/') + 1),
        preview: newInitialImage,
        isInitial: true,
      } as CustomFile;
    } else if (!newInitialImage) {
      currentInitialImage.value = null;
    }
  },
  { immediate: true }
);

function processFile(file: File): CustomFile {
  const customFile: CustomFile = file;
  if (isImage(customFile)) {
    customFile.preview = URL.createObjectURL(customFile);
  }
  return customFile;
}

function openFilePicker(): void {
  fileInput.value?.click();
}

function onFileChange(event: Event): void {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    const selectedFiles = Array.from(target.files);
    handleFiles(selectedFiles);
  }
}

function onDragOver(): void {
  isDragging.value = true;
}

function onDragLeave(): void {
  isDragging.value = false;
}

function onDrop(event: DragEvent): void {
  isDragging.value = false;
  if (event.dataTransfer) {
    const droppedFiles = Array.from(event.dataTransfer.files);
    handleFiles(droppedFiles);
  }
}

function handleFiles(files: File[]): void {
  let newFiles: CustomFile[] = files.filter((file) => {
    if (props.accept) {
      const acceptedTypes = props.accept.split(',').map((type) => type.trim());
      const fileType = file.type;
      const fileName = file.name;

      const isAccepted = acceptedTypes.some((acceptedType) => {
        if (acceptedType.endsWith('/*')) {
          return fileType.startsWith(acceptedType.slice(0, -1));
        }
        if (acceptedType.startsWith('.')) {
          return fileName.endsWith(acceptedType);
        }
        return fileType === acceptedType;
      });
      return isAccepted;
    }
    return true;
  }).map(processFile);

  if (!props.multiple) {
    newFiles = newFiles.slice(0, 1); // Only take the first file if not multiple
    internalFiles.value = newFiles;
  } else {
    internalFiles.value = [...internalFiles.value, ...newFiles];
  }

  updateModelValue();
}

function removeFile(index: number): void {
  internalFiles.value.splice(index, 1);
  updateModelValue();
}

function removeInitialImage(): void {
  currentInitialImage.value = null;
  emit('update:modelValue', null); // Emit null to clear the modelValue for initial image
}

function updateModelValue(): void {
  if (props.multiple) {
    emit('update:modelValue', internalFiles.value);
  } else {
    emit('update:modelValue', internalFiles.value.length > 0 ? internalFiles.value[0] : null);
  }
}

function isImage(file: File): boolean {
  return file.type.startsWith('image/');
}
</script>

<style scoped>
/* No scoped styles needed as Tailwind CSS is used */
</style>
