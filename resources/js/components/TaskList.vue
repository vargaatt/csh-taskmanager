<template>
    <manage-task :show="showDialog"
                 :creating-task="creatingTask"
                 :selected-task="selectedTask"
                 :user-list="userList"
                 :on-confirm="getSubmitFunction()"
                 @update:show="handleShowChange"></manage-task>
    <v-card class="mb-3">
        <v-card-text>
            <v-row>
                <v-col>
                    <h4>Selection summary:</h4>
                </v-col>
                <v-spacer></v-spacer>
                <v-col class="text-end">
                    <span class="mx-5">Selected estimate: {{ selectedEstimate }}</span>
                    <span>Selected spent: {{ selectedSpent }}</span>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
    <v-data-table
        :search="search"
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
                <v-spacer></v-spacer>
                <v-btn class="mb-2 my-2" color="primary" dark @click="openCreateModal">
                    Create task
                </v-btn>
                <v-btn class="mb-2 my-2" :disabled="0 === Object.values(selectedTasks).length" color="green" dark
                       @click="openCompleteModal">
                    Complete selected
                </v-btn>
                <v-btn class="mb-2 my-2" :disabled="0 === Object.values(selectedTasks).length" color="red" dark
                       @click="openDeleteModal">
                    Delete selected
                </v-btn>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    class="me-2 px-5"
                    density="compact"
                    label="Search"
                    prepend-inner-icon="mdi-magnify"
                    variant="solo-filled"
                    flat
                    hide-details
                    single-line
                ></v-text-field>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">Delete selected</v-card-title>
                        <v-card-text>
                            <div>Are you sure you want to delete the selected tasks?</div>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="close">Cancel</v-btn>
                            <v-btn color="red-darken-1" variant="text" @click="deleteTasks">Delete</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-dialog v-model="dialogComplete" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">Complete selected</v-card-title>
                        <v-card-text>
                            <div>Are you sure you want to complete the selected tasks?</div>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="close">Cancel</v-btn>
                            <v-btn color="green-darken-1" variant="text" @click="completeTasks">Complete</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon color="success" :disabled="null !== item.completed_date" class="me-2" size="small"
                    @click="openCompleteModal(item)">
                mdi-check
            </v-icon>
            <v-icon color="warning" class="" size="small" @click="openEditModal(item)">
                mdi-pencil
            </v-icon>
            <v-icon color="red" class="ms-2" size="small" @click="openDeleteModal(item)">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <span>No task has been recorded</span>
        </template>
    </v-data-table>
</template>

<script setup>
import {onMounted, ref, watch} from "vue";
import ManageTask from "./modals/ManageTask.vue";

const taskList = ref([]);
const userList = ref([]);
const selectedTasks = ref([]);
const search = ref('');
const dataTable = ref([]);
const sortBy = [{key: 'used_time', order: 'desc'}];
const taskToDelete = ref(null);
const showDialog = ref(false);
const selectedEstimate = ref(0);
const selectedSpent = ref(0);
const dialogDelete = ref(false);
const dialogComplete = ref(false);
const creatingTask = ref(true);
const selectedTask = ref({
    description: '',
    estimated_time: 0,
    used_time: 0,
});

const tableHeaders = ref([
    {title: 'Description', value: 'description', sortable: true},
    {title: 'Assigned to', value: 'assignee.name', sortable: true},
    {title: 'Time estimated', value: 'estimated_time', sortable: true, filterable: false},
    {title: 'Time spent', value: 'used_time', sortable: true, filterable: false},
    {title: 'Created', value: 'created_date', sortable: true},
    {title: 'Completed', value: 'completed_date', sortable: true},
    {title: 'Actions', key: 'actions', sortable: false, filterable: false},
]);

onMounted(async () => {
    await getUserList();
    await getUserTasks();
})

watch(() => selectedTasks.value, (newValue) => {
    selectedEstimate.value = getSumOfField(newValue, 'estimated_time');
    selectedSpent.value = getSumOfField(newValue, 'used_time');
});

async function getUserTasks() {
    const response = await axios.get('/user/tasks');
    taskList.value = response.data;
    dataTable.value = [];
    for (const task of taskList.value) {
        dataTable.value.push({
            id: task.id,
            description: task.description,
            assignee: {id: task.user.id, name: task.user.name},
            estimated_time: task.estimated_time,
            used_time: task.used_time,
            created_date: task.created_date,
            completed_date: task.completed_date,
        })
    }
}

async function getUserList() {
    const response = await axios.get('/users');
    for (const user of response.data) {
        userList.value.push({
            id: user.id,
            name: user.name
        })
    }
}

function getSumOfField(tasks, field) {
    return tasks.reduce((sum, task) => sum + (task[field] || 0), 0);
}

function openEditModal(item) {
    creatingTask.value = false;
    selectedTask.value = { ...item};
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

function openDeleteModal(task) {
    if (task) {
        selectedTask.value['id'] = task.id;
    }
    dialogDelete.value = true;
}

function openCompleteModal(task) {
    if (task) {
        selectedTask.value['id'] = task.id;
    }
    dialogComplete.value = true;
}

function getSubmitFunction() {
    return creatingTask.value ? createTask : editTask;
}

async function createTask() {
    await axios.post('/user/task/create', selectedTask.value);
    await getUserTasks();
    close();
}

async function editTask() {
    await axios.post('/user/task/' + selectedTask.value['id'], selectedTask.value);
    await getUserTasks();
    close();
}

async function deleteTasks() {
    const idsToDelete = selectedTask.value['id'] ? [selectedTask.value['id']] : selectedTasks.value.map(task => task.id);
    await axios.delete('/user/tasks', {
        data: {
            idsToDelete
        }
    });
    await getUserTasks();
    selectedTasks.value = selectedTasks.value.filter(task => !idsToDelete.includes(task.id));
    close();
}

async function completeTasks() {
    const idsToComplete = selectedTask.value['id'] ? [selectedTask.value['id']] : selectedTasks.value.map(task => task.id);
    await axios.post('/user/tasks/complete', {idsToComplete});
    await getUserTasks();
    selectedTasks.value = selectedTasks.value.filter(task => !idsToComplete.includes(task.id));
    close();
}

const handleShowChange = (newValue) => {
    showDialog.value = newValue;
};

function close() {
    showDialog.value = false;
    dialogDelete.value = false;
    dialogComplete.value = false;
    taskToDelete.value = null;
}
</script>
