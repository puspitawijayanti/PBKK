(function () {
    var levels = Array.prototype.map.call(document.querySelectorAll('#gradeScale tbody tr'), function (row) {
        return {
            min: parseFloat(row.dataset.min),
            grade: row.dataset.grade,
            weight: parseFloat(row.dataset.weight)
        };
    });

    function findLevel(score) {
        for (var i = 0; i < levels.length; i++) {
            if (score >= levels[i].min) {
                return levels[i];
            }
        }
        return null;
    }

    document.querySelectorAll('.score-input').forEach(function (input) {
        input.addEventListener('input', function () {
            var row = input.closest('tr');
            var gradeCell = row.querySelector('.grade-cell');
            var weightCell = row.querySelector('.weight-cell');
            var score = parseFloat(input.value);

            if (input.value === '' || isNaN(score) || score < 0 || score > 100) {
                gradeCell.innerHTML = '<span class="badge badge-light">Belum dinilai</span>';
                weightCell.textContent = '-';
                return;
            }

            var level = findLevel(score);
            gradeCell.innerHTML = '<span class="badge badge-secondary">' + level.grade + '</span>';
            weightCell.textContent = level.weight.toFixed(2);
        });
    });
})();
