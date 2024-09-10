<template>
    <v-dialog v-model="dialogModel" max-width="500px">
        <v-card v-if="'delete' === action">
            <v-card-title class="text-h5">Delete selected</v-card-title>
            <v-card-text>
                <div>Are you sure you want to delete the selected task(s)?</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDialog">Cancel</v-btn>
                <v-btn color="red-darken-1" variant="text" @click="onConfirm">Delete</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
        <v-card v-else>
            <v-card-title class="text-h5">Complete selected</v-card-title>
            <v-card-text>
                <div>Are you sure you want to complete the selected task(s)?</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDialog">Cancel</v-btn>
                <v-btn color="green-darken-1" variant="text" @click="onConfirm">Complete
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>

import {computed, toRefs, watch} from "vue";

const emit = defineEmits(['update:show']);
const props = defineProps({
    show: Boolean,
    selectedTasks: [Object, Array],
    onConfirm: Function,
    action: String
});

const {show} = toRefs(props);

const dialogModel = computed({
    get: () => props.show,
    set: (value) => emit('update:show', value),
});

watch(() => props.show, (newValue) => {
    dialogModel.value = newValue;
});

const closeDialog = () => {
    emit('update:show', false);
};

</script>
