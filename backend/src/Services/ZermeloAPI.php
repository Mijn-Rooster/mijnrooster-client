<?php

namespace api\Services;

use api\Services\ErrorHandler;

class ZermeloAPI {

    /**
     * Get schedule data from Zermelo API
     * @param string $user Id of the user to get the schedule for
     * @param int $start Start date of the schedule in UNIX timestamp
     * @param int $end End date of the schedule in UNIX timestamp
     * @param string $type Type of appointments to get. Check Zermelo API documentation for possible values
     * @param string $fields Fields to get from the appointments. Check Zermelo API documentation for possible values
     * @return array Lessons from schedule
     */
    public function getScheduleAppointments($user, $start, $end, $type = 'lesson,exam,oralExam,activity,talk,mixed,meeting,interlude', $fields = 'id,appointmentInstance,start,end,startTimeSlotName,endTimeSlotName,locations,teachers,subjects'): array {
        // Check if all required parameters are set
        if (!isset($user, $start, $end)) {
            ErrorHandler::handle("MISSING_PARAMETERS");
        }

        // Create query parameters
        $params = http_build_query([
            'valid' => "true",
            'cancelled' => "false",
            'user' => $user,
            'start' => $start,
            'end' => $end,
            'type' => $type,
            'fields' => $fields
        ]);

        $ch = curl_init(ZERMELO_PORTAL_URL . '/api/v3/appointments?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . ZERMELO_API_TOKEN
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true)['response'];
    }

    /**
     * Get user data from Zermelo API
     * @param string $studentId Id of the student to get the data for
     * @param string $schoolInSchoolYear Id of the school in the school year
     * @param string $fields Fields to get from the user data. Check Zermelo API documentation for possible values
     * @return array User data
     */
    public function getStudentDetails($studentId, $schoolInSchoolYear, $fields = 'student,firstName,prefix,lastName,mainGroupName,mainGroup,mentorGroup,departmentOfBranch') {
        // Check if all required parameters are set
        if (!isset($studentId, $schoolInSchoolYear)) {
            ErrorHandler::handle("MISSING_PARAMETERS");
        }

        // Create query parameters
        $params = http_build_query([
            'schoolInSchoolYear' => $schoolInSchoolYear,
            'fields' => $fields,
            'student' => $studentId
        ]);

        $ch = curl_init(ZERMELO_PORTAL_URL . '/api/v3/studentsindepartments?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . ZERMELO_API_TOKEN
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($response, true)['response'];
    }

    // To be implemented
    public function getTeacherDetails($teacherId, $schoolInSchoolYear, $fields = 'employee,firstName,prefix,lastName') {
        // Check if all required parameters are set
        if (!isset($teacherId, $schoolInSchoolYear)) {
            ErrorHandler::handle("MISSING_PARAMETERS");
        }

        // Create query parameters
        $params = http_build_query([
            'schoolInSchoolYear' => $schoolInSchoolYear,
            'fields' => $fields,
            'employee' => $teacherId
        ]);

        $ch = curl_init(ZERMELO_PORTAL_URL . '/api/v3/contracts?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . ZERMELO_API_TOKEN
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($response, true)['response'];
    }
}
