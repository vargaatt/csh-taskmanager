<template>
    <v-dialog v-model="dialogModel" max-width="500px" @click:outside="closeDialog">
        <v-card>
            <v-card-title>
                <span v-if="creatingTask" class="text-h5">{{ 'Create task' }}</span>
                <span v-else class="text-h5">{{ 'Edit task' }}</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-form ref="form">
                        <v-row>
                            <v-col cols="6" md="6" sm="6">
                                <v-text-field v-model="taskToManage.estimated_time" :rules="timeRule" type="number"
                                              label="Estimated time">
                                </v-text-field>
                            </v-col>
                            <v-col cols="6" md="6" sm="6">
                                <v-select
                                    v-model="taskToManage.assignee"
                                    label="Select"
                                    :items="userList"
                                    :rules="userRule"
                                    item-value="id"
                                    item-title="name"
                                    return-object
                                    single-line
                                    variant="solo-filled"
                                >
                                </v-select>
                            </v-col>
                            <v-col v-if="!creatingTask" :rules="timeRule" cols="6" md="6" sm="6">
                                <v-text-field v-model="taskToManage.used_time" type="number" label="Time spent">
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" md="12" sm="6">
                                <v-textarea :rules="descriptionRule" v-model="taskToManage.description"
                                            label="Description">
                                </v-textarea>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-container>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDialog"> Cancel</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="submit"> Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script setup>

import {computed, ref, toRefs, watch} from "vue";

const emit = defineEmits(['update:show']);
const props = defineProps({
    show: Boolean,
    creatingTask: Boolean,
    userList: [Object, Array],
    selectedTask: [Object, Array],
    onConfirm: Function,
})
const taskToManage = ref(null);
const form = ref(null);

const userRule = [value => {
    if (value) return true;
    return 'User have to be assigned to the task'
}];

const timeRule = [value => {
    if (0 < value) return true;
    return 'Time must be greater than 0';
}];

const descriptionRule = [value => {
    if (0 < value.length) return true;
    return 'Description is required'
}];

const {show} = toRefs(props);

const dialogModel = computed({
    get: () => props.show,
    set: (value) => emit('update:show', value),
});

watch(() => props.show, (newValue) => {
    taskToManage.value = props.selectedTask
    dialogModel.value = newValue;
});

async function submit() {
    const {valid} = await form.value.validate();
    if (valid) {
        props.onConfirm(taskToManage)
    }
}

const closeDialog = () => {
    emit('update:show', false);
};

</script>
