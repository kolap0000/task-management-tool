<template>
    <div class="relative p-2 flex overflow-x-auto h-full">
      <!-- Columns (Statuses) -->
      <div
        v-for="status in statuses"
        :key="status.slug"
        class="mr-6 w-4/5 max-w-xs flex-shrink-0"
      >
        <div class="rounded-md shadow-md overflow-hidden">
          <div class="p-3 flex justify-between items-baseline bg-blue-900 ">
            <h4 class="font-medium text-white">
              {{ status.title }}
            </h4>
            <button
              @click="openAddTaskForm(status.id)"
              class="py-1 px-2 text-sm text-white hover:underline"
            >
              ADD TASK
            </button>
          </div>
          <div class="p-2 bg-blue">
            <!-- AddTaskForm -->
            <AddTaskForm
              v-if="newTaskForStatus === status.id"
              :status-id="status.id"
              v-on:task-added="handleTaskAdded"
              v-on:task-canceled="closeAddTaskForm"
            />
            <!-- ./AddTaskForm -->
  
            <!-- Tasks -->
            <draggable
              class="flex-1 overflow-hidden"
              v-model="status.tasks"
              v-bind="taskDragOptions"
              @end="handleTaskMoved"
            >
            
              <transition-group class="flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto rounded shadow-xs" tag="div">
                
                
                <div v-for="task in status.tasks" :key="task.id"  class="mb-3 max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 transform hover:shadow-md cursor-pointer">
                    <UpdateTaskForm
                                    v-if="editTaskForStatus === task.id"
                                    :status-id="status.id"
                                    :task-id="task.id"
                                    :due-date="task.due_date"
                                    :title="task.title"
                                    :description="task.description"
                                    v-on:task-updated="handleTaskUpdated"
                                    v-on:task-canceled="closeEditTaskForm"
                                    />
                    <!-- <a href="#">
                        <img class="rounded-t-lg" src="images/image-1.jpg" alt="" />
                    </a> -->
                    <div class="p-5">
                        <a href="#" class="mb-3">
                            <h5 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">{{ task.title }}</h5>
                        </a>
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ task.description }}</p>
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Due: {{ task.due_date }}</p>
                        <a href="#"  @click="deleteTask(task.id,status.id)" class="mt-5 inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-900 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Delete
                        </a>
                        <a href="#"   @click="editTask(task.id,status.id)" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-900 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Edit
                        </a>

                    </div>
                </div>
                <!-- ./Tasks -->
              </transition-group>
            </draggable>
            <!-- No Tasks -->
            <div
              v-show="!status.tasks.length && newTaskForStatus !== status.id"
              class="flex-1 p-4 flex flex-col items-center justify-center"
            >
              <span class="text-gray-600">No tasks yet</span>
            </div>
            <!-- ./No Tasks -->
          </div>
        </div>
      </div>
      <!-- ./Columns -->
    </div>
  </template>
  
  <script>
  import draggable from "vuedraggable";
  import AddTaskForm from "./AddTaskForm";
  import UpdateTaskForm from "./UpdateTaskForm";
  
  export default {
    components: { draggable, AddTaskForm, UpdateTaskForm },
    props: {
      initialData: Array
    },
    data() {
      return {
        statuses: [],
        
        newTaskForStatus: 0,
        editTaskForStatus: 0,
        divs: []
      };
    },
    computed: {
      taskDragOptions() {
        return {
          animation: 200,
          group: "task-list",
          dragClass: "status-drag"
        };
      }
    },
    mounted() {
      // 'clone' the statuses so we don't alter the prop when making changes
      this.statuses = JSON.parse(JSON.stringify(this.initialData));
    },
    methods: {
      openAddTaskForm(statusId) {
        this.newTaskForStatus = statusId;
      },
      deleteTask(taskId,statusId) {

        let statusIndex = this.statuses.findIndex(
          status => status.id === statusId
        );

        let taskIndex = this.statuses[statusIndex].tasks.findIndex(
            tasks => tasks.id === taskId
        );

        this.statuses[statusIndex].tasks.splice(taskIndex, 1);

        axios.put("/api/tasks/delete",{ task_id: taskId  }).catch(err => {
          console.log(err.response);
        });

      },
      editTask(taskId,statusId) {
        this.editTaskForStatus = taskId;
      },
      closeAddTaskForm() {
        this.newTaskForStatus = 0;
      },
      closeEditTaskForm() {
        this.editTaskForStatus = 0;
      },
      handleTaskAdded(newTask) {
        // Find the index of the status where we should add the task
        const statusIndex = this.statuses.findIndex(
          status => status.id === newTask.status_id
        );

        // Add newly created task to our column
        this.statuses[statusIndex].tasks.push(newTask);
  
        // Reset and close the AddTaskForm
        this.closeAddTaskForm();
      },
      handleTaskUpdated(newTask) {
       console.log(newTask.status_id);

       let statusIndex = this.statuses.findIndex(
          status => status.id === newTask.status_id
        );

        let taskIndex = this.statuses[statusIndex].tasks.findIndex(
            tasks => tasks.id === newTask.task_id
        );

        this.statuses[statusIndex].tasks[taskIndex].title=newTask.title;
        this.statuses[statusIndex].tasks[taskIndex].description=newTask.description;
        this.statuses[statusIndex].tasks[taskIndex].due_date=newTask.due_date;


        this.closeEditTaskForm();
      },
      handleTaskMoved(evt) {
        axios.put("/api/tasks/sync", { columns: this.statuses }).catch(err => {
          console.log(err.response);
        });
      }
    }
  };
  </script>
  
  <style scoped>
  .status-drag {
    transition: transform 0.5s;
    transition-property: all;
  }
  </style>
  