<?php
function ch4_plugin_handle_project_edit()
{
    if (isset($_POST['update_project'])) {

        update_project_details();
    }
}

function update_project_details()
{
    global $wpdb;
    $project_series = sanitize_text_field($_POST['project_series']);
    $project_name = sanitize_text_field($_POST['project_name']);
    $project_status = sanitize_text_field($_POST['project_status']);
    $project_details = sanitize_text_field($_POST['project_details']);
    $table_name = $wpdb->prefix . 'ch4_projects';

    $data = [
        'name'    => $project_name,
        'status'  => $project_status,
        'details' => $project_details,
    ];

    $where = [
        'series' => $project_series,
    ];

    $format = [
        '%s', // name
        '%s', // status
        '%s', // details
    ];

    $wpdb->update($table_name, $data, $where, $format);
}



function load_projects_from_csv($csv_file_path)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ch4_projects';

    if (file_exists($csv_file_path)) {
        $csv_data = array_map('str_getcsv', file($csv_file_path));

        foreach ($csv_data as $row) {
            if ($row[0] != "") {
                $wpdb->insert(
                    $table_name,
                    array(
                        'series' => $row[0],
                        'name' => $row[1] ?? "",
                        'status' => $row[2] ?? "",
                        'details' => $row[3] ?? "",
                    )
                );
            }
        }
    }
}
function load_projects_from_excel($excel_file_path)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ch4_projects';

    if (file_exists($excel_file_path)) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel_file_path);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }
            if ($data[0] != "") {
                $wpdb->insert(
                    $table_name,
                    array(
                        'series' => $data[0],
                        'name' => $data[1] ?? "",
                        'status' => $data[2] ?? "",
                        'details' => $data[3] ?? "",
                    )
                );
            }
        }
    }
}


function handleFormProject()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ch4_projects';
    $wpdb->insert(
        $table_name,
        [
            'series' => $_POST["series"],
            'name' => $_POST["name"],
            'status' => $_POST["status"],
            'details' => $_POST["details"],
        ]
    );
    echo '<p style="color: green;">Proiectul a creat!</p>';
}

function handle_csv_upload()
{

    if (isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file'];

        if ($file['error'] == 0) {
            $file_name = sanitize_file_name($file['name']);
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . '/' . $file_name;

            // Check file extension
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if (in_array($file_extension, array('csv', 'xls', 'xlsx'))) {
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    if ($file_extension == 'csv') {
                        load_projects_from_csv($upload_path);
                    } else {
                        load_projects_from_excel($upload_path);
                    }
                    echo '<p style="color: green;">Fișierul a fost încărcat cu succes.</p>';
                } else {
                    echo '<p style="color: red;">A apărut o eroare, contactează suportul official@eltand.com</p>';
                }
            } else {
                echo '<p style="color: red;">Formatul de fișier nu este suportat. Sunt acceptate doar CSV, XLS și XLSX.</p>';
            }
        }
    }
}

function get_project_details_by_series($project_series)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ch4_projects'; // Replace 'your_projects_table' with your actual table name

    $project_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE series = %d", $project_series));

    return $project_details;
}

function get_projects_from_database()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'ch4_projects'; // Replace 'your_projects_table' with your actual table name

    $projects = $wpdb->get_results("SELECT series, name, status FROM $table_name");

    return $projects;
}