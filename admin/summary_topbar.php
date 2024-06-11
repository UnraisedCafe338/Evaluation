<style>
           .horizontal-nav {
            display: flex;
            justify-content: flex-start;
            background-color: none;
            margin-left: 10px;
            margin-top: 30px;
        }
        .horizontal-nav a {
            display: block;
            color: black;
            text-align: center;
            padding: 5px 10px;
            text-decoration: none;
            border-top-right-radius: 10px;
            border-top-left-radius: 10px;
            border: 3px solid #a0a0a0;
            border-top: 10px solid #ffd900;
            top: 100%;
            background-color: white;
        }
        .horizontal-nav a:hover {
            background-color: rgb(215, 215, 215);
        }
        .box-header{
            margin-top: -10px!important;
        }
</style>

<div class="horizontal-nav">
<a href="teacher_summary.php?FacultyID=<?php echo $facultyID;?>&academic_year_id=<?php echo $academic_year_id;?>&academic_year=<?php echo $academicYear;?>&semester=<?php echo $semester;?>" class="overall-button">Overall Summary</a>
<a href="subject_summary.php?FacultyID=<?php echo $facultyID;?>&academic_year_id=<?php echo $academic_year_id;?>&academic_year=<?php echo $academicYear;?>&semester=<?php echo $semester;?>" class="subject-button">Subject Summary</a>
<a href="section_summary.php?FacultyID=<?php echo $facultyID;?>&academic_year_id=<?php echo $academic_year_id;?>&academic_year=<?php echo $academicYear;?>&semester=<?php echo $semester;?>" class="section-button">Section Summary</a>

    </div>