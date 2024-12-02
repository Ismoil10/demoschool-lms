<?
/*
                      $question_log = db::arr("SELECT stql.*,
                          sq.id,
                          sq.question
                          FROM student_question_log AS stql
                          LEFT JOIN student_students AS st ON st.id = stql.student_id
                          LEFT JOIN student_questions AS sq ON sq.id = stql.question_id
                          LEFT JOIN student_modules AS sm ON sm.id = sq.module_id
                          LEFT JOIN student_sections AS ss ON ss.id = sm.section_id
                          LEFT JOIN student_courses AS sc ON sc.id = ss.course_id
                          WHERE st.user_id = '$student[ID]' AND (sq.try_number IS NOT NULL)");


                      $event_log = db::arr("SELECT id, `action`, comment, created_at FROM student_event_log WHERE user_id = '$student[ID]'
                                          UNION
                                          SELECT question_id, answer, correct, created_at FROM student_question_log WHERE student_id = '$student_on[id]'");
*/

?>


<!--<div class="col-md-12 mt-3">
                        <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                          <div class="card collapse-icon">
                            <div class="card-body">
                              <div class="collapse-margin">
                                <? foreach ($question_log as $id) : ?>
                                  <? $plus = $id['id'] ?>
                                  <div class="card">
                                    <div id="headingCollapseC<?= $plus ?>" class="card-header collapse-header" data-toggle="collapse" role="button" data-target="#collapseC<?= $plus ?>" aria-expanded="false" aria-controls="collapseC<?= $plus ?>">
                                      <div class="col-md-5">
                                        <h4>
                                          <div class="badge badge-light-primary">Kurs: <?= $id['name'] ?></div>
                                        </h4>
                                      </div>
                                    </div>
                                    <div id="collapseC<?= $plus ?>" role="tabpanel" aria-labelledby="headingCollapseC<?= $plus ?>" class="collapse" aria-expanded="false">
                                      <div class="card-body">
                                        <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                          <div class="collapse-icon">
                                            <div class="card-body">
                                              <div class="collapse-margin">
                                                <div class="card">
                                                  <div id="headingCollapseSs<?= $plus ?>" class="card-header collapse-header" data-toggle="collapse" role="button" data-target="#collapseSs<?= $plus ?>" aria-expanded="true" aria-controls="collapseSs<?= $plus ?>">
                                                    <div class="col-md-4">
                                                      <h4>
                                                        <div class="badge badge-light-primary">Bo'lim: <?= $id['section_title'] ?></div>
                                                      </h4>
                                                    </div>
                                                  </div>
                                                  <div id="collapseSs<?= $plus ?>" role="tabpanel" aria-labelledby="headingCollapseSs<?= $plus ?>" class="collapse show" aria-expanded="false">
                                                    <div class="card-body">
                                                      <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                                        <div class="collapse-icon">
                                                          <div class="card-body">
                                                            <div class="collapse-margin">
                                                              <div class="card">
                                                                <div id="headingCollapseSm<?= $plus ?>" class="card-header collapse-header" data-toggle="collapse" role="button" data-target="#collapseSm<?= $plus ?>" aria-expanded="true" aria-controls="collapseSm<?= $plus ?>">
                                                                  <div class="col-md-4">
                                                                    <h4>
                                                                      <div class="badge badge-light-primary">Mavzu: <?= $id['module_title'] ?></div>
                                                                    </h4>
                                                                  </div>
                                                                </div>
                                                                <div id="collapseSm<?= $plus ?>" role="tabpanel" aria-labelledby="headingCollapseSm<?= $plus ?>" class="collapse show" aria-expanded="false">
                                                                  <div class="card-body">
                                                                    <table class="table">
                                                                      <tbody>
                                                                        <tr>
                                                                          <td>
                                                                            <?= $id['question_id'] ?>
                                                                          </td>
                                                                          <td>
                                                                            <?= $id['created_at'] ?>
                                                                          </td>
                                                                          <td>
                                                                            <button type="button" onclick="deleteQuestion(<?=$id['question_id']?>)" class="btn btn-icon rounded-circle btn-outline-danger">
                                                                              <i data-feather="trash"></i>
                                                                            </button>
                                                                          </td>
                                                                        </tr>
                                                                      </tbody>
                                                                    </table>
                                                                  </div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <? endforeach; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>-->