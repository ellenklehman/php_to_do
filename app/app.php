<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    $app = new Silex\Application();

    $DB = new PDO('pgsql:host=localhost;dbname=to_do');

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()), 'tasks' => Task::getAll());
    });

    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $task = new Task($description, $id = null);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });

    $app->get("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', array('task' => $task, 'categories' => $task->categories(), 'all_categories' => Category::getAll()));
    });

    $app->patch("/tasks/{id}", function($id) use ($app) {
        $new_description = $_POST['description'];
        $task = Task::find($id);
        $task->update($new_description);
        return $app['twig']->render('task.hmtl.twig', array('task' => $task, 'tasks' => $task->categories()));
    });

    $app->delete("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $task->delete();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->get("/tasks/{id}/edit", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task_edit.html.twig', array('task' => $task));
    });

    $app->get("/add_categories", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $task->addCategory($category);
        return $app['twig']->render('task.html.twig', array('tasks' => Task::getAll(), 'categories' => $task->categories(), 'all_categories' => Category::getAll()));
    });

    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->post("/categories", function() use ($app) {
        $category = new Category($_POST['name']);
        $category->save();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->get("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->tasks(), 'all_tasks' => Task::getAll()));
    });

    $app->patch("/categories/{id}", function($id) use ($app) {
        $new_name = $_POST['name'];
        $category = Category::find($id);
        $category->update($new_name);
        return $app['twig']->render('category.hmtl.twig', array('category' => $category, 'tasks' => $category->tasks()));
    });

    $app->delete("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        $category->delete();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->get("/categories/{id}/edit", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category' => $category));
    });

    $app->get("/add_tasks", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', array('categories' => Category::getAll(), 'tasks' => $category->tasks(), 'all_tasks' => Task::getAll()));
    });

    $app->post("/delete_categories", function() use ($app) {
        Category::deleteAll();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });

    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('index.html.twig', array('categories' => Category::getAll(), 'tasks' => Task::getAll()));
    });


    return $app;
?>
