<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager</title>
    <link rel="stylesheet" href="/../public/styles.css">
</head>

<body>
    <header>
        <h1>Contact Manager</h1>
        <ul>
            <li><a href="/../public/index.php">Home</a></li>
            <li><a href="/../public/table.html">Contacts</a></li>
        </ul>
    </header>

    <main id="main-1">

        <section id="contact-form">
            <h2>Add Contact</h2>
            <form id="contactForm" method="post">
                <?php
                require_once __DIR__ . '/../public/form.php';


                $fieldConfigs = [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Name'
                    ],
                    [
                        'type' => 'number',
                        'name' => 'phone',
                        'label' => 'Phone Number'
                    ],
                    [
                        'type' => 'email',
                        'name' => 'email',
                        'label' => 'Email ID'
                    ],
                    [
                        'type' => 'text',
                        'name' => 'address',
                        'label' => 'Address'
                    ]

                ];

                $form = new Form($fieldConfigs);
                echo $form->render();
                ?>

                <button type="submit">Save</button>
            </form>
        </section>
    </main>

   

    <script src="/../public/script1.js"></script>
</body>

</html>
