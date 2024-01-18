<?php

function ch4_plugin_display_project_details()
{
    if (isset($_POST['project_series']) && !empty($_POST['project_series'])) {
        $project_series = sanitize_text_field($_POST['project_series']);
        $project_details = (new ProjectService())->get_project_details_by_series($project_series);
        if ($project_details) {
            echo responseSearch($project_details);
        } else {
            echo '<p>Proiectul cu seria ' . esc_html($project_series) . ' nu a fost gasit</p>';
        }
    }
}
function responseSearch($project_details)
{
?>

<div class="responseETD">
    <div class="reponseHeaderETD">
        <span><i>Serie:<?php echo $project_details->series ?></i></span>
    </div>
    <div class="responseContentETD">
        <div class="preContentETD">
            <h3><b><?php echo $project_details->name ?></b></h3>
            <p><b>Status: </b><?php echo $project_details->status ?> </p>
        </div>
        <hr>
        <p><b>Descriere:</b></p>
        <p><?php echo $project_details->details ?></p>

    </div>
</div>
<?php
}
function ch4_plugin_edit_project_form()
{
    $project_series = sanitize_text_field($_GET['series']);
    $project_details = (new ProjectService())->get_project_details_by_series($project_series);

    if ($project_details) {
    ?>
<h3>Editeaza Proiect</h3>
<div class="col">
    <form method="post" class="form-ETD">
        <input type="hidden" name="project_series" value="<?php echo esc_attr($project_details->series); ?>"
            maxlength="191">
        <label for="project_name">Numele proiectului:</label>
        <input type="text" name="project_name" value="<?php echo esc_attr($project_details->name); ?>" maxlength="191">
        <label for="project_status">Statusul proiectului:</label>
        <input type="text" name="project_status" value="<?php echo esc_attr($project_details->status); ?>"
            maxlength="191">
        <label for="project_details">Descrierea proiectului:</label>
        <textarea name="project_details" id="" cols="30" rows="10" class="descFieldETD">
    <?php echo esc_attr($project_details->details); ?>
    </textarea>
        <input type="submit" name="update_project" value="Update" class="button button-primary">
    </form>
</div>
<?php
    }
}


function ch4_admin()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $default_tab = null;
    $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
    (new ProjectService())->ch4_plugin_handle_project_edit();
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['series'])) {

        ch4_plugin_edit_project_form();
    } else {
    ?>
<div class="wrap">
    <nav class="nav-tab-wrapper">
        <a href="?page=ch4" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Proiecte</a>
        <a href="?page=ch4&tab=add" class="nav-tab <?php if ($tab === 'add') : ?>nav-tab-active<?php endif; ?>">Adauga
            Proiecte</a>
    </nav>
    <div class="tab-content">
        <?php switch ($tab):
                    case 'add':
                        addProjectsPage();
                        break;
                    default:
                        backoffice_main_page();
                        break;
                endswitch; ?>
    </div>
</div>
<?php
    }
}

function ch4_plugin_project_search_form()
{
    ob_start();
    ?>
<div class="flex-container-ETD">
    <form action="" method="post" class="form-ETD">
        <label for="project_series">Serie Proiect:</label>
        <input type="text" name="project_series" id="project_series" maxlength="191">
        <input type="submit" value="Cauta">
    </form>
</div>
<?php
    ch4_plugin_display_project_details();
    return ob_get_clean();
}

function backoffice_main_page()
{
    $nonce = wp_create_nonce('wp_rest');
?>
<div class="wrap">
    <hr>
    <table class="ch4ProjectTable">
        <thead>
            <tr>
                <th>Serie</th>
                <th>Nume</th>
                <th>Status</th>
                <th>Actiune</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $projects = (new ProjectService())->get_projects_from_database();

                foreach ($projects as $project) {
                    echo '<tr>';
                    echo '<td>' . esc_html($project->series) . '</td>';
                    echo '<td>' . esc_html($project->name) . '</td>';
                    echo '<td>' . esc_html($project->status) . '</td>';
                    echo '<td><a href="?page=ch4&action=edit&series=' . esc_attr($project->series) . '">Editeaza</a>, <a href="#" class="deleteProject" data-nonce="' . $nonce . '" data-id="' . esc_attr($project->series) . '">Sterge</a></td>'; // Edit link
                    echo '</tr>';
                }
                ?>
        </tbody>
    </table>
    <script>
    jQuery(document).ready(function() {
        jQuery(".ch4ProjectTable").DataTable({
            autoWidth: false,
            ordering: true,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ro.json',
            },
        });
    })
    </script>
</div>
<?php
}

function addProjectsPage()
{
    if (isset($_POST['upload_csv'])) {
        (new ProjectService())->handle_csv_upload();
    } elseif (isset($_POST['update_project'])) {
        (new ProjectService())->ch4_plugin_handle_project_edit();
    } elseif (isset($_POST['addSingleProject'])) {
        (new ProjectService())->handleFormProject();
    }
?>
<div class="flex-container-ETD">
    <div class="col-6-responsive">
        <h3>Incarca fisier</h3>
        <form method="post" enctype="multipart/form-data" class="form-upload-ETD">
            <label for="ch4Files" class="drop-container" id="dropcontainer">
                <span class="drop-title dashicons-before dashicons-cloud-upload">Lasa aici</span>
                Sau
                <input type="file" name="csv_file" id="ch4Files" accept=".csv,.xls,.xlsx" required>
            </label>
            <input type="submit" name="upload_csv" value="Trimite" class="button button-primary">


        </form>
    </div>
    <div class="col-6-responsive">
        <h3>
            Introduce Date Manual
        </h3>
        <form method="post" enctype="multipart/form-data" class="form-ETD ">
            <label for="series">Serie</label>
            <input type="text" name="series" id="series" maxlength="191">
            <br>
            <label for="name">Nume Proiect</label>
            <input type="text" name="name" id="name" maxlength="191">
            <br>
            <label for="status">Status</label>
            <input type="text" name="status" id="status" maxlength="191">
            <br>
            <label for="details">Descriere</label>
            <textarea name="details" id="" cols="30" rows="10"></textarea>
            <input type="submit" name="addSingleProject" value="Incarca" class="button button-primary">
        </form>
    </div>
</div>
<?php
}