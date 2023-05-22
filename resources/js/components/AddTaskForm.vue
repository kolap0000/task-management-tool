<template>
    <form
      class="relative mb-3 flex flex-col justify-between bg-white rounded-md shadow overflow-hidden"
      @submit.prevent="handleAddNewTask"
    >
      <div class="p-3 flex-1">
        <span class="block w-full px-2 py-1 text-lg rounded">Add</span>
        <input
          class="block w-full px-2 py-1 text-lg border rounded mb-3"
          type="date"
          placeholder="Enter a Due Date"
          v-model.trim="newTask.due_date"
        />
        <input
          class="block w-full px-2 py-1 text-lg border rounded mb-3"
          type="text"
          placeholder="Enter a title"
          v-model.trim="newTask.title"
        />
        <textarea
          class="mt-3 p-2 block w-full p-1 border text-sm rounded"
          rows="2"
          placeholder="Add a description (optional)"
          v-model.trim="newTask.description"
        ></textarea>
        
        <div v-show="errorMessage">
          <span class="text-xs text-red-500">
            {{ errorMessage }}
          </span>
        </div>
      </div>
      <div class="p-3 flex justify-between items-end text-sm bg-gray-100">
        
        <button
          type="submit"
          class="px-3 py-1 leading-5 text-white bg-blue-900 hover:bg-blue-800 rounded"
        >
          Add
        </button>
        <button
          @click="$emit('task-canceled')"
          type="reset"
          class="py-1 leading-5 text-gray-600 hover:text-gray-700"
        >
          Cancel
        </button>
      </div>
    </form>
  </template>
  
  <script>
  export default {
    props: {
      statusId: Number
    },
    data() {
      return {
        newTask: {
          title: "",
          description: "",
          status_id: null,
          due_date: "",
        },
        errorMessage: ""
      };
    },
    mounted() {
      this.newTask.status_id = this.statusId;
    },
    methods: {
      handleAddNewTask() {
        if (!this.newTask.due_date) {
          this.errorMessage = "The due date is required";
          return;
        }

        if (!this.newTask.title) {
          this.errorMessage = "The title field is required";
          return;
        }
  
        // Send new task to server
        axios
          .post("/api/tasks", this.newTask)
          .then(res => {
            this.$emit("task-added", res.data);
          })
          .catch(err => {
            this.handleErrors(err);
          });
      },
      handleErrors(err) {
        if (err.response && err.response.status === 422) {
          const errorBag = err.response.data.errors;
          if (errorBag.title) {
            this.errorMessage = errorBag.title[0];
          } else if (errorBag.description) {
            this.errorMessage = errorBag.description[0];
          } else {
            this.errorMessage = err.response.message;
          }
        } else {
          console.log(err.response);
        }
      }
    }
  };
  </script>
  