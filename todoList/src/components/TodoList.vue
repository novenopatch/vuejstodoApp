<template>
  <div class="todo-app">
    <h1 class="title">Welcome to the Todo List App</h1>
    <div class="form-container">
      <form @submit.prevent="addTodo" class="todo-form">
        <input 
          v-model="newTodoTitle" 
          placeholder="Add a new todo" 
          class="todo-input"
        />
        <button type="submit" class="todo-button" :disabled="newTodoTitle.trim().length === 0">
          Add
        </button>
      </form>
    </div>

    <div v-if="todoList.length === 0" class="empty-message">
      <p>No todos yet. Add your first todo!</p>
    </div>

    <div v-else>
      <label class="hide-completed">
        <input 
          type="checkbox" 
          v-model="hideCompletedValue" 
          class="todo-checkbox"
        />
        Hide completed todos
      </label>

      <ul class="todo-list">
        <li v-for="todo in filteredTodoList" :key="todo.id" class="todo-item">
          <span :class="{ completed: todo.completed }" class="todo-title">
            {{ todo.title }}
          </span>
          <input 
            type="checkbox" 
            v-model="todo.completed" 
            @change="updateTodoStatus(todo)" 
            class="todo-checkbox"
          />
          <button @click="deleteTodo(todo.id)" class="delete-button">
            Delete
          </button>
        </li>
      </ul>

      <p v-if="remainingTodos > 0" class="empty-message">
        {{ remainingTodos }} tasks remaining.
      </p>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const API_URL = 'http://127.0.0.1:8000/api/todos';

    const todoList = ref([]);
    const newTodoTitle = ref('');
    const hideCompletedValue = ref(false);

    const filteredTodoList = computed(() => {
      const sortedTodos = [...todoList.value].sort((a, b) => a.completed - b.completed);
      return hideCompletedValue.value ? sortedTodos.filter(todo => !todo.completed) : sortedTodos;
    });

    const remainingTodos = computed(() => {
      return todoList.value.filter(todo => !todo.completed).length;
    });

    const fetchTodosFromAPI = async () => {
      try {
        const response = await axios.get(API_URL);
        todoList.value = response.data;
      } catch (error) {
        console.error('Error fetching todos:', error);
      }
    };

    const addTodo = async () => {
      if (newTodoTitle.value.trim() === '') return;

      const newTodo = {
        title: newTodoTitle.value,
        completed: false,
      };

      try {
        const response = await axios.post(API_URL, newTodo);
        todoList.value.push(response.data);
        newTodoTitle.value = '';
      } catch (error) {
        console.error('Error adding todo:', error);
      }
    };

    const updateTodoStatus = async (todo) => {
      try {
        await axios.put(`${API_URL}/${todo.id}`, { completed: todo.completed });
      } catch (error) {
        console.error('Error updating todo:', error);
      }
    };

    const deleteTodo = async (id) => {
      try {
        await axios.delete(`${API_URL}/${id}`);
        todoList.value = todoList.value.filter(todo => todo.id !== id);
      } catch (error) {
        console.error('Error deleting todo:', error);
      }
    };

    onMounted(() => {
      fetchTodosFromAPI();
    });

    return {
      todoList,
      newTodoTitle,
      hideCompletedValue,
      filteredTodoList,
      remainingTodos,
      addTodo,
      deleteTodo,
      updateTodoStatus,
    };
  },
};
</script>

<style scoped>
.todo-app {
  font-family: Arial, sans-serif;
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.title {
  text-align: center;
  font-size: 24px;
  margin-bottom: 20px;
  color: #333;
}

.form-container {
  margin-bottom: 20px;
}

.todo-form {
  display: flex;
  gap: 10px;
}

.todo-input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.todo-button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.todo-button:hover {
  background-color: #0056b3;
}

.empty-message {
  text-align: center;
  color: #999;
}

.todo-list {
  list-style: none;
  padding: 0;
  color: #999;
}

.todo-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 10px;
  background-color: #fff;
}

.todo-title {
  flex: 1;
  margin-right: 10px;
}

.todo-checkbox {
  margin-right: 10px;
}

.delete-button {
  padding: 5px 10px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.delete-button:hover {
  background-color: #a71d2a;
}

.completed {
  opacity: 0.5;
  text-decoration: line-through;
  color: red;
}

.hide-completed {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
  font-size: 16px;
  color: #333;
}

.hide-completed input[type="checkbox"] {
  cursor: pointer;
}
</style>
