<?php
// Reuse the form view for both create and edit
$isEdit = isset($program);
$program = $program ?? null;
echo view('admin.programs.form', compact('departments', 'program', 'isEdit'))->render();
