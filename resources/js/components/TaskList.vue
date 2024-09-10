<template>
    <manage-task :show="dialogManage"
                 :creating-task="creatingTask"
                 :selected-task="selectedTask"
                 :user-list="userList"
                 :on-confirm="getSubmitFunction()"
                 @update:show="(newValue) => dialogManage = newValue"></manage-task>
    <handle-task :show="dialogDelete"
                 action="delete"
                 :selected-tasks="selectedTasks"
                 :on-confirm="() => handleTasks('delete')"
                 @update:show="(newValue) => dialogDelete = newValue"></handle-task>
    <handle-task :show="dialogComplete"
                 action="complete"
                 :selected-tasks="selectedTasks"
                 :on-confirm="() => handleTasks('complete')"
                 @update:show="(newValue) => dialogComplete = newValue"></handle-task>
    <v-card class="mb-3">
        <v-card-text>
            <v-row>
                <v-col>
                    <h4>Selection summary:</h4>
                </v-col>
                <v-spacer></v-spacer>
                <v-col class="text-end">
                    <span class="mx-5">Selected estimate: {{ selectedEstimate }}h</span>
                    <span>Selected spent: {{ selectedSpent }}h</span>
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
                <v-toolbar-title>Tasks</v-toolbar-title>
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

            </v-toolbar>
        </template>
        <template v-slot:item.description="{ item }">
            <span class=""> {{ item.description }} </span>
        </template>
        <template #item.estimated_time="{ item }">
            {{ item.estimated_time }}h
        </template>
        <template #item.used_time="{ item }">
            {{ item.used_time }}h
        </template>
        <template v-slot:item.actions="{ item }">
            <div class="actions-width ms-auto">
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
            </div>
        </template>
        <template v-slot:no-data>
            <span>No task has been recorded</span>
        </template>
    </v-data-table>
</template>

<script setup>
import {onMounted, ref, watch} from "vue";
import ManageTask from "./modals/ManageTask.vue";
import HandleTask from "./modals/HandleTask.vue";

const userList = ref([]);
const selectedTasks = ref([]);
const search = ref('');
const dataTable = ref([]);
const sortBy = [{key: 'used_time', order: 'desc'}];
const dialogManage = ref(false);
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
    {title: 'Time estimated', value: 'estimated_time', sortable: true, filterable: false, align: 'center'},
    {title: 'Time spent', value: 'used_time', sortable: true, filterable: false, align: 'center'},
    {title: 'Created', value: 'created_date', sortable: true, align: 'center'},
    {title: 'Completed', value: 'completed_date', sortable: true, align: 'center'},
    {title: 'Actions', key: 'actions', sortable: false, filterable: false, align: 'end'},
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
    const selectedIds = selectedTasks.value.map(item => item.id);
    const updatedTaskIds = response.data.map(task => task.id);

    response.data.forEach(task => {
        const itemToUpdate = dataTable.value.find(item => item.id === task.id);

        const taskData = {
            id: task.id,
            description: task.description,
            assignee: {id: task.user.id, name: task.user.name},
            estimated_time: task.estimated_time,
            used_time: task.used_time,
            created_date: task.created_date,
            completed_date: task.completed_date
        };

        if (itemToUpdate) {
            Object.assign(itemToUpdate, taskData);
        } else {
            dataTable.value.push(taskData);
        }
    });

    dataTable.value = dataTable.value.filter(item => updatedTaskIds.includes(item.id));
    selectedTasks.value = dataTable.value.filter(item => selectedIds.includes(item.id));
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
    selectedTask.value = {...item};
    dialogManage.value = true;
}

function openCreateModal() {
    creatingTask.value = true;
    selectedTask.value['id'] = '';
    selectedTask.value['description'] = '';
    selectedTask.value['estimated_time'] = 1;
    selectedTask.value['used_time'] = 1;
    selectedTask.value['assignee'] = null;

    dialogManage.value = true;
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
    const action = creatingTask.value ? 'create' : 'edit';

    return () => {
        saveTask(action)
    };
}

async function saveTask(action) {
    const endpoint = action === 'create' ? '/user/task/create' : '/user/task/' + selectedTask.value['id'];
    await axios.post(endpoint, selectedTask.value);
    await getUserTasks();

    closeDialogs();
}

async function handleTasks(action) {
    const ids = selectedTask.value['id'] ? [selectedTask.value['id']] : selectedTasks.value.map(task => task.id);
    const endpoint = action === 'delete' ? '/user/tasks' : '/user/tasks/complete';
    const method = action === 'delete' ? 'delete' : 'post';
    const data = action === 'delete' ? {data: {ids}} : {ids};

    await axios[method](endpoint, data);
    await getUserTasks();

    selectedTasks.value = selectedTasks.value.filter(task => !ids.includes(task.id));
    closeDialogs();
}

function closeDialogs() {
    dialogManage.value = false;
    dialogDelete.value = false;
    dialogComplete.value = false;
}
</script>
