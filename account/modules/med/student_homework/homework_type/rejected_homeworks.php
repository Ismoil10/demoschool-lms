<?

$homeworks = db::arr("SELECT 
sqp.id AS id,
sqp.status AS `status`,
DATE_FORMAT(sqp.created_at, '%Y-%m-%d %H:%i') AS `date`,
ss.id AS student_id,
ss.username AS username,
sl.id AS lesson_id,
sl.title AS title
FROM student_question_practice sqp
RIGHT JOIN student_students AS ss ON ss.id = sqp.student_id
RIGHT JOIN student_questions AS sq ON sq.id = sqp.question_id
RIGHT JOIN student_lessons AS sl ON sl.id = sq.lesson_id
WHERE sqp.teacher_id = '$teacher_id[ID]' AND sqp.status = 'rejected'");

$statuses = ["uploaded" => "Yuklangan", "rejected" => "Rad etilgan", "approved" => "Baholangan"];
$statuses_background = ["uploaded" => "primary", "rejected" => "warning", "approved" => "success"];
?>
<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">Vazifalar ro'yhati</h4>
        <div class="dt-action-buttons text-right">
            <div class="dt-buttons d-inline-flex">
                <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" onclick="add_modal()">Yangi qo'shish</button>
                <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter"><i data-feather="filter"></i></button>
            </div>
        </div>
    </div>
    <div class="card-datatable">
        <table class="d_tab dt-responsive table" id="customtable">
            <thead>
                <tr>
                    <th></th>
                    <th>#</th>
                    <th>Sana</th>
                    <th>Talaba</th>
                    <th>Mavzu</th>
                    <th>Vazifa turi</th>
                    <th>Holat</th>
                    <th>Harakat</th>
                </tr>
            </thead>
            <tbody>
            <? foreach ($homeworks as $v) : ?>
                        <tr>
                            <td></td>
                            <td><?= $index++ ?></td>
                            <td><?= $v['date'] ?></td>
                            <td><?= $v['username'] ?></td>
                            <td><?= $v['title'] ?></td>
                            <td><?php if($v['url'] != ''): ?>
                                    <a href="<?php echo $v['url']; ?>">Vazifa</a>
                                <?php else: ?>
                                    <p></p>
                                <? endif; ?>
                            </td>
                            <td><span class="badge badge-pill badge-light-<?= $statuses_background[$v["status"]] ?>"><?= $statuses[$v["status"]] ?></span></td>
                            <td>
                                <button type="button" onclick="checkHomework(<?=$v['id']?>)" class="btn btn-icon btn-icon rounded-circle btn-primary">
                                    <i data-feather="pen-tool"></i>
                                </button>
                            </td>
                        </tr>
                    <? endforeach; ?>
            </tbody>
        </table>
    </div>
</div>.