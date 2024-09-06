<template>
    <v-dialog v-model="dialogModel" max-width="500px" @click:outside="closeDialog">
        <v-card>
            <v-card-title>
                <span v-if="creatingTask" class="text-h5">{{ 'Create task' }}</span>
                <span v-else class="text-h5">{{ 'Edit task' }}</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="6" md="6" sm="6">
                            <v-text-field v-model="selectedTask.estimated_time" label="Estimated time">
                            </v-text-field>
                        </v-col>
                        <v-col v-if="!creatingTask" cols="6" md="6" sm="6">
                            <v-text-field v-model="selectedTask.used_time" label="Time spent">
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="12" sm="6">
                            <v-textarea v-model="selectedTask.description" label="Description">
                            </v-textarea>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDialog"> Cancel</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="onConfirm"> Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>

import {computed, toRefs, watch} from "vue";

const emit = defineEmits(['update:show']);
const props = defineProps({
    show: Boolean,
    creatingTask: Boolean,
    selectedTask: [Object, Array],
    onConfirm: Function,
})

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
