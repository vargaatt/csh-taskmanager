<template>
    <v-app>
        <v-main>
            <v-container>
                <v-row>
                    <v-col cols="12" md="12">
                        <TaskList></TaskList>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import TaskList from "./TaskList.vue";

export default {
    name: 'App',
    components: {TaskList},
};
</script>
