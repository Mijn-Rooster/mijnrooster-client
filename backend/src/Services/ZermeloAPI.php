<?php

namespace api\Services;

// Require the necessary services
use api\Services\ErrorHandler;

/**
 * Zermelo API Service
 * This class is responsible for handling all requests to the Zermelo API.
 */

class ZermeloAPI {

    /**
     * Get schedule data from Zermelo API
     * @param string $user Id of the user to get the schedule for (e.g. 545959 or "GIJS")
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

        // Filter out invalid characters
        $start = (int)$start;
        $end = (int)$end;

        // Check if the start and end date are valid
        // If the start date is in the future, it is invalid
        // The maximum difference between the start and end date is 62 days (5356800 seconds)
        if ($start > $end || (($end - $start) > 5356800)) {
            ErrorHandler::handle("SCHEDULE_INVALID_DATE");
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
     * @param string $studentId Id of the student to get the data for (e.g.545959)
     * @param string $schoolInSchoolYear Id of the school in the school year
     * @param string $fields Fields to get from the user data. Check Zermelo API documentation for possible values
     * @return array User data
     */
    public function getStudentDetails($studentId, $schoolInSchoolYear, $fields = 'student,firstName,prefix,lastName,mainGroupName,mainGroup,mentorGroup,departmentOfBranch') {
        // Check if all required parameters are set
        if (!isset($studentId, $schoolInSchoolYear)) {
            ErrorHandler::handle("MISSING_PARAMETERS");
        }

        // Filter out invalid characters
        $schoolInSchoolYear = (int)$schoolInSchoolYear;

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

    /**
     * Get teacher data from Zermelo API
     * @param string $teacherId Id of the teacher to get the data for (e.g. "GIJS")
     * @param int $schoolInSchoolYear Id of the school in the school year
     * @param string $fields Fields to get from the teacher data. Check Zermelo API documentation for possible values
     * @return array Teacher data
     */
    public function getTeacherDetails($teacherId, $schoolInSchoolYear, $fields = 'employee,firstName,prefix,lastName'): array {
        // Check if all required parameters are set
        if (!isset($teacherId, $schoolInSchoolYear)) {
            ErrorHandler::handle("MISSING_PARAMETERS");
        }

        // Filter out invalid characters
        $schoolInSchoolYear = (int)$schoolInSchoolYear;

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
