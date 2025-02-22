<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h1 class="text-center">To-Do List</h1>

    <div class="form-check mb-3">
        <input type="checkbox" id="showAllCheckbox" class="form-check-input" onclick="toggleShowAll()">
        <label for="showAllCheckbox" class="form-check-label">Show All Tasks</label>
    </div>
    
    <div class="input-group mb-3">
        <input type="text" id="taskInput" class="form-control" placeholder="Enter a task">
        <button class="btn btn-primary" onclick="addTask()">Add Task</button>
    </div>
    
    
    
    <ul id="taskList" class="list-group"></ul>

    <script>
        document.addEventListener('DOMContentLoaded', () => loadTasks());

        async function addTask() {
            const taskInput = document.getElementById('taskInput').value.trim();
            if (!taskInput) {
                alert("Task cannot be empty!");
                return;
            }

            try {
                const response = await fetch('/task/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ task: taskInput })
                });

                const result = await response.json();
                alert(result.message);
                document.getElementById('taskInput').value = '';
                loadTasks();
            } catch (error) {
                alert("Error adding task.");
            }
        }

        async function loadTasks(showAll = false) {
            try {
                const response = await fetch('/tasks/all');
                const tasks = await response.json();
                const taskList = document.getElementById('taskList');
                taskList.innerHTML = '';

                tasks.forEach(task => {
                    if (!showAll && task.completed) return;

                    const li = document.createElement('li');
                    li.className = "list-group-item d-flex justify-content-between align-items-center";
                    li.innerHTML = `
                        <div>
                            <input type="checkbox" class="form-check-input me-2" onclick="toggleTask(${task.id})" ${task.completed ? 'checked' : ''}>
                            <span class="${task.completed ? 'text-decoration-line-through text-muted' : ''}">${task.task}</span>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="deleteTask(${task.id})">Delete</button>
                    `;
                    taskList.appendChild(li);
                });
            } catch (error) {
                alert("Error loading tasks.");
            }
        }

        async function toggleTask(id) {
            try {
                const response = await fetch(`/task/toggle/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                const result = await response.json();
                alert(result.message);
                loadTasks(document.getElementById('showAllCheckbox').checked);
            } catch (error) {
                alert("Error updating task.");
            }
        }

        async function deleteTask(id) {
            if (!confirm("Are you sure to delete this task?")) return;

            try {
                const response = await fetch(`/task/delete/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                const result = await response.json();
                alert(result.message);
                loadTasks(document.getElementById('showAllCheckbox').checked);
            } catch (error) {
                alert("Error deleting task.");
            }
        }

        function toggleShowAll() {
            loadTasks(document.getElementById('showAllCheckbox').checked);
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
