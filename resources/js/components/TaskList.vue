<template>
    <manage-task :show="showDialog"
                 :creating-task="creatingTask"
                 :selected-task="selectedTask"
                 :on-confirm="getSubmitFunction()"
                 @update:show="handleShowChange"></manage-task>

    <v-data-table
        show-select
        v-model="selectedTasks"
        :headers="tableHeaders"
        :items="dataTable"
        item-value="id"
        items-per-page="-1"
        return-object
        :sort-by="sortBy"
    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>My Tasks</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-btn class="mb-2" color="primary" dark @click="openCreateModal">
                    Create task
                </v-btn>
                <v-btn class="mb-2" :disabled="0 === Object.values(selectedTasks).length" color="red" dark
                       @click="openDeleteModal">
                    Delete selected
                </v-btn>

                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">Delete selected</v-card-title>
                        <v-card-text>
                            <div>Are you sure you want to delete the selected tasks?</div>
                            <div>({{ Object.values(selectedTasks).length }} selected)</div>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="close">Cancel</v-btn>
                            <v-btn color="blue-darken-1" variant="text" @click="deleteTask">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon color="success" class="me-2" size="small" @click="openEditModal(item)">
                mdi-check
            </v-icon>
            <v-icon color="warning" class="" size="small" @click="openEditModal(item)">
                mdi-pencil
            </v-icon>
        </template>
        <template v-slot:no-data>
            <span>No task has been recorded</span>
        </template>
    </v-data-table>
</template>

<script setup>
import {onMounted, ref} from "vue";
import ManageTask from "./modals/ManageTask.vue";

const taskList = ref([]);
const selectedTasks = ref([]);
const dataTable = ref([]);
const sortBy = [{key: 'used_time', order: 'asc'}];
const taskToDelete = ref(null);
const showDialog = ref(false);
const dialogDelete = ref(false);
const creatingTask = ref(true);
const selectedTask = ref({
    description: '',
    estimated_time: 0,
    used_time: 0,
});

const tableHeaders = ref([
    {title: 'Description', value: 'description', sortable: true},
    {title: 'Time estimated', value: 'estimated_time', sortable: true},
    {title: 'Time spent', value: 'used_time', sortable: true},
    {title: 'Created', value: 'created_date', sortable: true},
    {title: 'Completed', value: 'completed_date', sortable: true},
    {title: 'Actions', key: 'actions', sortable: false},
]);

onMounted(async () => {
    await getUserTasks();
})

async function getUserTasks() {
    const response = await axios.get('user/tasks');
    taskList.value = response.data;
    dataTable.value = [];
    for (const task of taskList.value) {
        dataTable.value.push({
            id: task.id,
            description: task.description,
            estimated_time: task.estimated_time,
            used_time: task.used_time,
            created_date: task.created_date,
            completed_date: task.completed_date,
        })
    }
}

function openEditModal(item) {
    creatingTask.value = false;
    selectedTask.value['id'] = item.id;
    selectedTask.value['description'] = item.description;
    selectedTask.value['estimated_time'] = item.estimated_time;
    selectedTask.value['used_time'] = item.used_time;
    showDialog.value = true;
}

function openCreateModal() {
    creatingTask.value = true;
    selectedTask.value['id'] = '';
    selectedTask.value['description'] = '';
    selectedTask.value['estimated_time'] = 0;
    selectedTask.value['used_time'] = 0;
    showDialog.value = true;
}

function openDeleteModal() {
    dialogDelete.value = true;
}

function getSubmitFunction() {
    return creatingTask.value ? createTask : editTask;
}

async function createTask() {
    await axios.post('/user/tasks/create', selectedTask.value);
    await getUserTasks();
    close();
}

async function editTask() {
    await axios.post('/user/tasks/' + selectedTask.value['id'], selectedTask.value);
    await getUserTasks();
    close();
}

async function deleteTask() {
    const idsToDelete = selectedTasks.value.map(task => task.id);
    await axios.delete('/user/tasks', {
        data: {
            idsToDelete
        }
    });
    selectedTasks.value = [];
    await getUserTasks();
    close();
}

const handleShowChange = (newValue) => {
    showDialog.value = newValue;
};

function close() {
    showDialog.value = false;
    dialogDelete.value = false;
    taskToDelete.value = null;
}
</script>
