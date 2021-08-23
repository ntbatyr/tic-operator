<?php
/**
 * @var $USER
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$iPrograms = \Models\InvestProgram::all();
?>
    <div class="container-fluid">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Минимальный взнос</th>
                    <th>Годовой процент</th>
                    <th>Активность</th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($iPrograms as $p) { ?>
                    <tr>
                        <td><?=$p->id;?></td>
                        <td><?=$p->name;?></td>
                        <td><?=number_format($p->min_deposit, 2, '.', ' ');?></td>
                        <td><?=$p->annual_percent;?></td>
                        <td><?=$p->active == 1 ? 'Активный' : 'Не активный';?></td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-start align-items-center">
            <div class="col-12 col-md-6">
                <form method="post" action="/admin/investments/new/">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Название</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="min_deposit" class="form-label">Минимальный взнос</label>
                                <input type="text" name="min_deposit" id="min_deposit" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="annual_percent" class="form-label">Годовой процент</label>
                                <input type="text" name="annual_percent" id="annual_percent" class="form-control" required>
                            </div>

                            <div class="d-flex justify-content-center mt-5">
                                <button type="submit" class="btn btn-success px-5">
                                    Создать
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");