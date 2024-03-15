<section class="content">
    <form>
        <div class="block block-themed">
            <div class="block-header bg-primary">
                <h3 class="block-title">Filter</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-2">
                        <label for="year">Year</label>
                        <select class="form-control" id="year" name="year" required>
                            <?php if (isset($years)): ?>
                                <option value="">Select Year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= echo htmlspecialchars($year['id']); ?>" <?php if (isset($year_id) && $year['id'] == $year_id): echo 'selected'; endif; ?>>
                                        <?= echo htmlspecialchars($year['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="month">Month</label>
                        <select class="form-control" id="month" name="month">
                            <?php if (isset($months)): ?>
                                <option value="">Select Month</option>
                                <?php foreach ($months as $month): ?>
                                    <option value="<?= echo htmlspecialchars($month['id']); ?>" <?php if (isset($month_id) && $month['id'] == $month_id): echo 'selected'; endif; ?>>
                                        <?= echo htmlspecialchars($month['name']); ?>
                                    </option>
                              
