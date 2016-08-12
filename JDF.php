<?php

namespace irantarikhjdf\jdf;

/**
 * This is just an example.
 */
class jdf extends \yii\base\Widget
{

    /**
     * Set current timezone
     */
    private function _setTimeZone()
    {
        if($this->timeZone != 'local') {
            date_default_timezone_set($this->timeZone == '' ? 'Asia/Tehran' : $this->timeZone);
        }
    }

    /**
     * Displays current Jalali date
     * @param string $format The date format
     * @param int $timeStamp Desired time stamp
     * @param string $language The translation language of numbers
     * @return string The requested date
     */

    public function date($format, $timeStamp = '', $language = 'fa')
    {
        $secondsCorrection = 0; // Server time correction, +/- seconds difference
        $this->_setTimeZone();
        $ts = $secondsCorrection + (($timeStamp == '' or $timeStamp == 'now') ? time() : $this->trNum($timeStamp));
        $date = explode('_', date('H_i_j_n_O_P_s_w_Y', $ts));
        list($jalaliYear, $jalaliMonth, $jalaliDay) = $this->gregorianToJalali($date[8], $date[3], $date[2]);
        $dayOfYear = ($jalaliMonth < 7) ? (($jalaliMonth - 1) * 31) + $jalaliDay - 1 : (($jalaliMonth - 7) * 30) + $jalaliDay + 185;
        $leapYear = ($this->isLeapYear($jalaliYear) ? 1 : 0);
        $length = mb_strlen($format);
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $currentChar = mb_substr($format, $i, 1);
            if($currentChar == '\\') {
                $result .= mb_substr($format, ++$i, 1);
                continue;
            }
            switch($currentChar) {
                case 'E':
                case 'R':
                case 'x':
                case 'X':
                    $result .= 'http://jdf.scr.ir';
                    break;
                case 'B':
                case 'e':
                case 'g':
                case 'G':
                case 'h':
                case 'I':
                case 'T':
                case 'u':
                case 'Z':
                    $result .= date($currentChar, $ts);
                    break;
                case 'a':
                    $result .= ($date[0] < 12) ? 'ق.ظ' : 'ب.ظ';
                    break;
                case 'A':
                    $result .= ($date[0] < 12) ? 'قبل از ظهر' : 'بعد از ظهر';
                    break;
                case 'b':
                    $result .= (int)($jalaliMonth / 3.1) + 1;
                    break;
                case 'c':
                    $result .= $jalaliYear . '/' . $jalaliMonth . '/' . $jalaliDay . ' ،' . $date[0] . ':' . $date[1] . ':' . $date[6] . ' ' . $date[5];
                    break;
                case 'C':
                    $result .= (int)(($jalaliYear + 99) / 100);
                    break;
                case 'd':
                    $result .= ($jalaliDay < 10) ? '0' . $jalaliDay : $jalaliDay;
                    break;
                case 'D':
                    $result .= $this->dateWords(array('kh' => $date[7]), ' ');
                    break;
                case 'f':
                    $result .= $this->dateWords(array('ff' => $jalaliMonth), ' ');
                    break;
                case 'F':
                    $result .= $this->dateWords(array('mm' => $jalaliMonth), ' ');
                    break;
                case 'H':
                    $result .= $date[0];
                    break;
                case 'i':
                    $result .= $date[1];
                    break;
                case 'j':
                    $result .= $jalaliDay;
                    break;
                case 'J':
                    $result .= $this->dateWords(array('rr' => $jalaliDay), ' ');
                    break;
                case 'k';
                    $result .= $this->trNum(100 - (int)($dayOfYear / ($leapYear + 365) * 1000) / 10, $language);
                    break;
                case 'K':
                    $result .= $this->trNum((int)($dayOfYear / ($leapYear + 365) * 1000) / 10, $language);
                    break;
                case 'l':
                    $result .= $this->dateWords(array('rh' => $date[7]), ' ');
                    break;
                case 'L':
                    $result .= $leapYear;
                    break;
                case 'm':
                    $result .= ($jalaliMonth > 9) ? $jalaliMonth : '0' . $jalaliMonth;
                    break;
                case 'M':
                    $result .= $this->dateWords(array('km' => $jalaliMonth), ' ');
                    break;
                case 'n':
                    $result .= $jalaliMonth;
                    break;
                case 'N':
                    $result .= $date[7] + 1;
                    break;
                case 'o':
                    $jalaliWeekDays = ($date[7] == 6) ? 0 : $date[7] + 1;
                    $daysOfNextYear = 364 + $leapYear - $dayOfYear;
                    $result .= ($jalaliWeekDays > ($dayOfYear + 3) && $dayOfYear < 3) ? $jalaliYear - 1 : (((3 - $daysOfNextYear) > $jalaliWeekDays and $daysOfNextYear < 3) ? $jalaliYear + 1 : $jalaliYear);
                    break;
                case 'O':
                    $result .= $date[4];
                    break;
                case 'p':
                    $result .= $this->dateWords(array('mb' => $jalaliMonth), ' ');
                    break;
                case 'P':
                    $result .= $date[5];
                    break;
                case 'q':
                    $result .= $this->dateWords(array('sh' => $jalaliYear), ' ');
                    break;
                case 'Q':
                    $result .= $leapYear + 364 - $dayOfYear;
                    break;
                case 'r':
                    $key = $this->dateWords(array('rh' => $date[7], 'mm' => $jalaliMonth));
                    $result .= $date[0] . ':' . $date[1] . ':' . $date[6] . ' ' . $date[4] . ' ' . $key['rh'] . '، ' . $jalaliDay . ' ' . $key['mm'] . ' ' . $jalaliYear;
                    break;
                case 's':
                    $result .= $date[6];
                    break;
                case 'S':
                    $result .= 'ام';
                    break;
                case 't':
                    $result .= ($jalaliMonth != 12) ? (31 - (int)($jalaliMonth / 6.5)) : ($leapYear + 29);
                    break;
                case 'U':
                    $result .= $ts;
                    break;
                case 'v':
                    $result .= $this->dateWords(array('ss' => mb_substr($jalaliYear, 2, 2)), ' ');
                    break;
                case 'V':
                    $result .= $this->dateWords(array('ss' => $jalaliYear), ' ');
                    break;
                case 'w':
                    $result .= ($date[7] == 6) ? 0 : $date[7] + 1;
                    break;
                case 'W':
                    $nextYearStartDay = (($date[7] == 6) ? 0 : $date[7] + 1) - ($dayOfYear % 7);
                    if($nextYearStartDay < 0) {
                        $nextYearStartDay += 7;
                    }
                    $weekNumber = (int)(($dayOfYear + $nextYearStartDay) / 7);
                    if($nextYearStartDay < 4) {
                        $weekNumber++;
                    }
                    elseif($weekNumber < 1) {
                        $weekNumber = ($nextYearStartDay == 4 || $nextYearStartDay == (($jalaliYear % 33 % 4 - 2 == (int)($jalaliYear % 33 * .05)) ? 5 : 4)) ? 53 : 52;
                    }
                    $currenyYearEndDay = $nextYearStartDay + $leapYear;
                    if($currenyYearEndDay == 7) {
                        $currenyYearEndDay = 0;
                    }
                    $result .= (($leapYear + 363 - $dayOfYear) < $currenyYearEndDay && $currenyYearEndDay < 3) ? '01' : (($weekNumber < 10) ? '0' . $weekNumber : $weekNumber);
                    break;
                case 'y':
                    $result .= mb_substr($jalaliYear, 2, 2);
                    break;
                case 'Y':
                    $result .= $jalaliYear;
                    break;
                case 'z':
                    $result .= $dayOfYear;
                    break;
                default:
                    $result .= $currentChar;
                    break;
            }
        }
        return ($language != 'en' ? $this->trNum($result, 'fa', '.') : $result);
    }

    /**
     * Converts timestamp to formatted string
     * @param string $format The string format
     * @param int $timeStamp Desired time stamp
     * @param string $language The translation language of numbers
     * @return string The formatted date
     */
    public function strftime($format, $timeStamp = '', $language = 'fa')
    {
        $secondsCorrection = 0; // Server time correction, +/- seconds difference
        $this->_setTimeZone();
        $ts = $secondsCorrection + (($timeStamp == '' or $timeStamp == 'now') ? time() : $this->trNum($timeStamp));
        $date = explode('_', date('h_H_i_j_n_s_w_Y', $ts));
        list($jalaliYear, $jalaliMonth, $jalaliDay) = $this->gregorianToJalali($date[7], $date[4], $date[3]);
        $daysOfYear = ($jalaliMonth < 7) ? (($jalaliMonth - 1) * 31) + $jalaliDay - 1 : (($jalaliMonth - 7) * 30) + $jalaliDay + 185;
        $leapYear = $this->isLeapYear($jalaliYear);
        $length = mb_strlen($format);
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $currentChar = mb_substr($format, $i, 1);
            if($currentChar == '%') {
                $currentChar = mb_substr($format, ++$i, 1);
            }
            else {
                $result .= $currentChar;
                continue;
            }
            switch($currentChar) {
                /* Day */
                case 'a':
                    $result .= $this->dateWords(array('kh' => $date[6]), ' ');
                    break;
                case'A':
                    $result .= $this->dateWords(array('rh' => $date[6]), ' ');
                    break;
                case 'd':
                    $result .= ($jalaliDay < 10 ? '0' . $jalaliDay : $jalaliDay);
                    break;
                case 'e':
                    $result .= ($jalaliDay < 10 ? ' ' . $jalaliDay : $jalaliDay);
                    break;
                case 'j':
                    $result .= str_pad($daysOfYear + 1, 3, 0, STR_PAD_LEFT);
                    break;
                case 'u':
                    $result .= $date[6] + 1;
                    break;
                case 'w':
                    $result .= ($date[6] == 6) ? 0 : $date[6] + 1;
                    break;
                case 'U':
                    $lastWeekDayOfYear = (($date[6] < 5) ? $date[6] + 2 : $date[6] - 5) - ($daysOfYear % 7);
                    if($lastWeekDayOfYear < 0) {
                        $lastWeekDayOfYear += 7;
                    }
                    $weekNumber = (int)(($daysOfYear + $lastWeekDayOfYear) / 7) + 1;
                    if($lastWeekDayOfYear > 3 or $lastWeekDayOfYear == 1)
                        $weekNumber--;
                    $result .= ($weekNumber < 10 ? '0' . $weekNumber : $weekNumber);
                    break;
                case 'V':
                    $lastWeekDayOfYear = (($date[6] == 6) ? 0 : $date[6] + 1) - ($daysOfYear % 7);
                    if($lastWeekDayOfYear < 0) {
                        $lastWeekDayOfYear+=7;
                    }
                    $weekNumber = (int)(($daysOfYear + $lastWeekDayOfYear) / 7);
                    if($lastWeekDayOfYear < 4) {
                        $weekNumber++;
                    }
                    elseif($weekNumber < 1) {
                        $weekNumber = ($lastWeekDayOfYear == 4 || $lastWeekDayOfYear == (($jalaliYear % 33 % 4 - 2 == (int)($jalaliYear % 33 * .05)) ? 5 : 4)) ? 53 : 52;
                    }
                    $lastWeekDay = $lastWeekDayOfYear + $leapYear;
                    if($lastWeekDay == 7)
                        $lastWeekDay = 0;
                    $result.=(($leapYear + 363 - $daysOfYear) < $lastWeekDay and $lastWeekDay < 3) ? '01' : (($weekNumber < 10) ? '0' . $weekNumber : $weekNumber);
                    break;
                case 'W':
                    $lastWeekDayOfYear = (($date[6] == 6) ? 0 : $date[6] + 1) - ($daysOfYear % 7);
                    if($lastWeekDayOfYear < 0) {
                        $lastWeekDayOfYear += 7;
                    }
                    $weekNumber = (int)(($daysOfYear + $lastWeekDayOfYear) / 7) + 1;
                    if($lastWeekDayOfYear > 3) {
                        $weekNumber--;
                    }
                    $result .= ($weekNumber < 10) ? '0' . $weekNumber : $weekNumber;
                    break;
                case 'b':
                case 'h':
                    $result .= $this->dateWords(array('km' => $jalaliMonth), ' ');
                    break;
                case 'B':
                    $result .= $this->dateWords(array('mm' => $jalaliMonth), ' ');
                    break;
                case 'm':
                    $result .= ($jalaliMonth > 9) ? $jalaliMonth : '0' . $jalaliMonth;
                    break;
                case 'C':
                    $result .= mb_substr($jalaliYear, 0, 2);
                    break;
                case 'g':
                    $jalaliWeekDay = ($date[6] == 6) ? 0 : $date[6] + 1;
                    $daysOfNextYear = 364 + $leapYear - $daysOfYear;
                    $result .= mb_substr(($jalaliWeekDay > ($daysOfYear + 3) && $daysOfYear < 3) ? $jalaliYear - 1 : (((3 - $daysOfNextYear) > $jalaliWeekDay && $daysOfNextYear < 3) ? $jalaliYear + 1 : $jalaliYear), 2, 2);
                    break;
                case 'G':
                    $jalaliWeekDay = ($date[6] == 6) ? 0 : $date[6] + 1;
                    $daysOfNextYear = 364 + $leapYear - $daysOfYear;
                    $result .= ($jalaliWeekDay > ($daysOfYear + 3) && $daysOfYear < 3) ? $jalaliYear - 1 : (((3 - $daysOfNextYear) > $jalaliWeekDay && $daysOfNextYear < 3) ? $jalaliYear + 1 : $jalaliYear);
                    break;
                case 'y':
                    $result .= mb_substr($jalaliYear, 2, 2);
                    break;
                case 'Y':
                    $result .= $jalaliYear;
                    break;
                case 'H':
                    $result .= $date[1];
                    break;
                case 'I':
                    $result .= $date[0];
                    break;
                case 'l':
                    $result .= ($date[0] > 9) ? $date[0] : ' ' . (int)$date[0];
                    break;
                case 'M':
                    $result .= $date[2];
                    break;
                case 'p':
                    $result .= ($date[1] < 12) ? 'قبل از ظهر' : 'بعد از ظهر';
                    break;
                case 'P':
                    $result .= ($date[1] < 12) ? 'ق.ظ' : 'ب.ظ';
                    break;
                case 'r':
                    $result .= $date[0] . ':' . $date[2] . ':' . $date[5] . ' ' . (($date[1] < 12) ? 'قبل از ظهر' : 'بعد از ظهر');
                    break;
                case 'R':
                    $result .= $date[1] . ':' . $date[2];
                    break;
                case 'S':
                    $result .= $date[5];
                    break;
                case 'T':
                    $result .= $date[1] . ':' . $date[2] . ':' . $date[5];
                    break;
                case 'X':
                    $result .= $date[0] . ':' . $date[2] . ':' . $date[5];
                    break;
                case 'z':
                    $result .= $this->date('O', $ts);
                    break;
                case 'Z':
                    $result .= $this->date('T', $ts);
                    break;
                case 'c':
                    $key = $this->dateWords(array('rh' => $date[6], 'mm' => $jalaliMonth));
                    $result .= $date[1] . ':' . $date[2] . ':' . $date[5] . ' ' . $this->date('P', $ts) . ' ' . $key['rh'] . '، ' . $jalaliDay . ' ' . $key['mm'] . ' ' . $jalaliYear;
                    break;
                case 'D':
                    $result .= mb_substr($jalaliYear, 2, 2) . '/' . ($jalaliMonth > 9 ? $jalaliMonth : '0' . $jalaliMonth) . '/' . ($jalaliDay < 10 ? '0' . $jalaliDay : $jalaliDay);
                    break;
                case 'F':
                    $result .= $jalaliYear . '-' . ($jalaliMonth > 9 ? $jalaliMonth : '0' . $jalaliMonth) . '-' . ($jalaliDay < 10 ? '0' . $jalaliDay : $jalaliDay);
                    break;
                case 's':
                    $result .= $ts;
                    break;
                case 'x':
                    $result .= mb_substr($jalaliYear, 2, 2) . '/' . ($jalaliMonth > 9 ? $jalaliMonth : '0' . $jalaliMonth) . '/' . ($jalaliDay < 10 ? '0' . $jalaliDay : $jalaliDay);
                    break;
                case 'n':
                    $result .= PHP_EOL;
                    break;
                case 't':
                    $result .= "\t";
                    break;
                case '%':
                    $result .= '%';
                    break;
                default:
                    $result .= $currentChar;
                    break;
            }
        }
        return ($language != 'en' ? $this->trNum($result, 'fa', '.') : $result);
    }

    /**
     * Makes timestamp from given jalali info
     * @param int $hour hour
     * @param int $minute minute
     * @param int $second second
     * @param int $jalaliMonth Jalali month
     * @param int $jajaliDay Jalali day
     * @param int $jalaliYear Jalali year
     * @param int|boolean $isDST Daylight saving time
     * @return int The calculated time stamp
     */
    public function mktime($hour = '', $minute = '', $second = '', $jalaliMonth = '', $jajaliDay = '', $jalaliYear = '', $isDST = -1)
    {
        $hour = $this->trNum($hour);
        $minute = $this->trNum($minute);
        $second = $this->trNum($second);
        $jalaliMonth = $this->trNum($jalaliMonth);
        $jajaliDay = $this->trNum($jajaliDay);
        $jalaliYear = $this->trNum($jalaliYear);
        if($hour == '' && $minute == '' && $second == '' && $jalaliMonth == '' && $jajaliDay == '' && $jalaliYear == '') {
            return mktime();
        }
        else {
            list($year, $minute, $day) = $this->jalaliToGregorian($jalaliYear, $jalaliMonth, $jajaliDay);
            return mktime($hour, $minute, $second, $minute, $day, $year, $isDST);
        }
    }

    /**
     * Gets the Jalali info of given timestamp
     * @param int $timeStamp The requested time stamp
     * @param string $trNum The translation language of numbers
     * @return array Jalali info as an array
     */
    function getDate($timeStamp = '', $trNum = 'en')
    {
        $ts = ($timeStamp == '') ? time() : tr_num($timeStamp);
        $jDate = explode('_', $this->date('F_G_i_j_l_n_s_w_Y_z', $ts, '', $this->timeZone, $trNum));
        return array(
            'seconds' => $this->trNum((int)$this->trNum($jDate[6]), $trNum),
            'minutes' => $this->trNum((int)$this->trNum($jDate[2]), $trNum),
            'hours' => $jDate[1],
            'mday' => $jDate[3],
            'wday' => $jDate[7],
            'mon' => $jDate[5],
            'year' => $jDate[8],
            'yday' => $jDate[9],
            'weekday' => $jDate[4],
            'month' => $jDate[0],
            0 => $this->trNum($ts, $trNum)
        );
    }

    /**
     * Checks to see whether the given date is valid or not
     * @param int $jalaliMonth Jalali month
     * @param int $jalaliDay Jalali day
     * @param int $jalaliYear Jalali year
     * @return boolean The check result of date validation
     */
    public function checkDate($jalaliMonth, $jalaliDay, $jalaliYear)
    {
        $jalaliMonth = $this->trNum($jalaliMonth);
        $jalaliDay = $this->trNum($jalaliDay);
        $jalaliYear = $this->trNum($jalaliYear);
        $lastDayOfMonth = ($jalaliMonth == 12) ? ($this->isLeapYear($jalaliYear) ? 30 : 29) : 31 - (int)($jalaliMonth / 6.5);
        return ($jalaliMonth > 0 && $jalaliDay > 0 && $jalaliYear > 0 && $jalaliMonth < 13 && $jalaliDay <= $lastDayOfMonth);
    }

    /**
     * Converts numbers of a string to English of Farsi
     * @param string $string The string to convert
     * @param string $language Target language (en|fa)
     * @param string $digitSeparator The replacement character for digit separator
     * @return string The converted string
     */
    public function trNum($string, $language = 'en', $digitSeparator = '٫')
    {
        $latinNumbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
        $farsiNumbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $digitSeparator);
        if($language == 'fa') {
            return str_replace($latinNumbers, $farsiNumbers, $string);
        }
        return str_replace($farsiNumbers, $latinNumbers, $string);
    }

    /**
     * Converts date to word equivalence
     * @param array $dateElements The date elements
     * @param string $glue The glue used to convert array to string
     * @return array|string The converted date as an string
     */
    public function dateWords($dateElements, $glue = '')
    {
        foreach($dateElements as $key => $value) {
            $value = (int)$this->trNum($value);
            switch($key) {
                case 'ss':
                    $lastSecondChar = mb_substr($value, -2, 1);
                    $tens = $tensAndOnes = $ones = '';
                    if($lastSecondChar == 1) {
                        $separator = '';
                        $tenToTwenty = array('ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده');
                        $tensAndOnes = $tenToTwenty[mb_substr($value, -2, 2) - 10];
                    }
                    else {
                        $lastThirdChar = mb_substr($value, -3, 1);
                        $separator = ($lastSecondChar == 0 or $lastThirdChar == 0) ? '' : ' و ';
                        $tenWords = array('', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود');
                        $tens = $tenWords[$lastSecondChar];
                        $oneWords = array('', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه');
                        $ones = $oneWords[$lastThirdChar];
                    }
                    $yearNumbers = array('12', '13', '14', '19', '20');
                    $yearWords = array('هزار و دویست', 'هزار و سیصد', 'هزار و چهارصد', 'هزار و نهصد', 'دوهزار');
                    $dateElements[$key] = (($value > 99) ? str_ireplace($yearNumbers, $yearWords, mb_substr($value, 0, 2)) . ((mb_substr($value, 2, 2) == '00') ? '' : ' و ') : '') . $tenWords . $separator . $tensAndOnes . $ones;
                    break;
                case 'mm':
                    $monthNames = array('فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند');
                    $dateElements[$key] = $monthNames[$value - 1];
                    break;
                case 'rr':
                    $dayWords = array('یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه', 'ده', 'یازده', 'دوازده', 'سیزده',
                        'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده', 'بیست', 'بیست و یک', 'بیست و دو', 'بیست و سه',
                        'بیست و چهار', 'بیست و پنج', 'بیست و شش', 'بیست و هفت', 'بیست و هشت', 'بیست و نه', 'سی', 'سی و یک');
                    $dateElements[$key] = $dayWords[$value - 1];
                    break;
                case 'rh':
                    $weekDays = array('یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه');
                    $dateElements[$key] = $weekDays[$value];
                    break;
                case 'sh':
                    $animalYears = array('مار', 'اسب', 'گوسفند', 'میمون', 'مرغ', 'سگ', 'خوک', 'موش', 'گاو', 'پلنگ', 'خرگوش', 'نهنگ');
                    $dateElements[$key] = $animalYears[$value % 12];
                    break;
                case 'mb':
                    $ancientMonths = array('حمل', 'ثور', 'جوزا', 'سرطان', 'اسد', 'سنبله', 'میزان', 'عقرب', 'قوس', 'جدی', 'دلو', 'حوت');
                    $dateElements[$key] = $ancientMonths[$value - 1];
                    break;
                case 'ff':
                    $seasons = array('بهار', 'تابستان', 'پاییز', 'زمستان');
                    $dateElements[$key] = $seasons[$value - 1];
                    break;
                case 'km':
                    $briefMonths = array('فر', 'ار', 'خر', 'تی‍', 'مر', 'شه‍', 'مه‍', 'آب‍', 'آذ', 'دی', 'به‍', 'اس‍');
                    $dateElements[$key] = $briefMonths[$value - 1];
                    break;
                case 'kh':
                    $briefDays = array('ی', 'د', 'س', 'چ', 'پ', 'ج', 'ش');
                    $dateElements[$key] = $briefMonths[$value];
                    break;
                default:
                    $dateElements[$key] = $value;
                    break;
            }
        }
        return ($glue == '' ? $dateElements : implode($glue, $dateElements));
    }

    /**
     * Converts Gregorian date to Jalali
     * @param int $gregorianYear Gregorian year
     * @param int $gregorialMonth Gregorian month
     * @param int $gregorianDay Gregorian day
     * @param string $glue The glue to convert the result to string
     * @return array|string The Jalali date
     */
    function gregorianToJalali($gregorianYear, $gregorialMonth, $gregorianDay, $glue = '')
    {
        $gregorianYear = $this->trNum($gregorianYear);
        $gregorialMonth = $this->trNum($gregorialMonth);
        $gregorianDay = $this->trNum($gregorianDay);
        $yearReminderOfFour = $gregorianYear % 4;
        $elapsedDaysOfYear = array(0, 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        $gregorianDaysOfYear = $elapsedDaysOfYear[(int)$gregorialMonth] + $gregorianDay;
        if($yearReminderOfFour == 0 && $gregorialMonth > 2) {
            $gregorianDaysOfYear++;
        }
        $leapDay = (int)((($gregorianYear - 16) % 132) * .0305);
        $after = ($leapDay == 3 or $leapDay < ($yearReminderOfFour - 1) or $yearReminderOfFour == 0) ? 286 : 287;
        $before = (($leapDay == 1 or $leapDay == 2) and ( $leapDay == $yearReminderOfFour or $yearReminderOfFour == 1)) ? 78 : (($leapDay == 3 and $yearReminderOfFour == 0) ? 80 : 79);
        if((int)(($gregorianYear - 10) / 63) == 30) {
            $after--;
            $before++;
        }
        if($gregorianDaysOfYear > $before) {
            $jalaliYear = $gregorianYear - 621;
            $jalaliDaysOfYear = $gregorianDaysOfYear - $before;
        }
        else {
            $jalaliYear = $gregorianYear - 622;
            $jalaliDaysOfYear = $gregorianDaysOfYear + $after;
        }
        if($jalaliDaysOfYear < 187) {
            $jalaliMonth = (int)(($jalaliDaysOfYear - 1) / 31);
            $jalaliDay = $jalaliDaysOfYear - (31 * $jalaliMonth++);
        }
        else {
            $jalaliMonth = (int)(($jalaliDaysOfYear - 187) / 30);
            $jalaliDay = $jalaliDaysOfYear - 186 - ($jalaliMonth * 30);
            $jalaliMonth += 7;
        }
        $result = array($jalaliYear, $jalaliMonth, $jalaliDay);
        return ($glue == '' ? $result : implode($glue, $result));
    }

    function tr_num($str,$mod='en',$mf='٫'){
        $num_a=array('0','1','2','3','4','5','6','7','8','9','.');
        $key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹',$mf);
        return($mod=='fa')?str_replace($num_a,$key_a,$str):str_replace($key_a,$num_a,$str);
    }


    function jalali_to_gregorian($jy,$jm,$jd,$mod=''){
        $jy=$jy; $jm=$jm; $jd=$jd;/* <= Extra :اين سطر ، جزء تابع اصلي نيست */
        $gy=($jy<=979)?621:1600;
        $jy-=($jy<=979)?0:979;
        $days=(365*$jy) +(((int)($jy/33))*8) +((int)((($jy%33)+3)/4))
            +78 +$jd +(($jm<7)?($jm-1)*31:(($jm-7)*30)+186);
        $gy+=400*((int)($days/146097));
        $days%=146097;
        if($days > 36524){
            $gy+=100*((int)(--$days/36524));
            $days%=36524;
            if($days >= 365)$days++;
        }
        $gy+=4*((int)(($days)/1461));
        $days%=1461;
        $gy+=(int)(($days-1)/365);
        if($days > 365)$days=($days-1)%365;
        $gd=$days+1;
        foreach(array(0,31,(($gy%4==0 and $gy%100!=0) or ($gy%400==0))?29:28
                ,31,30,31,30,31,31,30,31,30,31) as $gm=>$v){
            if($gd<=$v)break;
            $gd-=$v;
        }
        return($mod=='')?array($gy,$gm,$gd):$gy.$mod.$gm.$mod.$gd;
    }







    /**
     * Converts Jalali date to Gregorian
     * @param int $jalaliYear Jalali year
     * @param int $jalaliMonth Jalali month
     * @param int $jalaliDay Jalali day
     * @param string $glue The glue to convert the result to string
     * @return array|string The Gregorian date
     */
    function jalaliToGregorian($jalaliYear, $jalaliMonth, $jalaliDay, $glue = '')
    {
        $jalaliYear = $this->trNum($jalaliYear);
        $jalaliMonth = $this->trNum($jalaliMonth);
        $jalaliDay = $this->trNum($jalaliDay);
        $yearRemindeOfFour = ($jalaliYear + 1) % 4;
        $jalaliDaysOfYear = ($jalaliMonth < 7) ? (($jalaliMonth - 1) * 31) + $jalaliDay : (($jalaliMonth - 7) * 30) + $jalaliDay + 186;
        $leapDay = (int)((($jalaliYear - 55) % 132) * .0305);
        $after = ($leapDay != 3 and $yearRemindeOfFour <= $leapDay) ? 287 : 286;
        $before = (($leapDay == 1 or $leapDay == 2) and ( $leapDay == $yearRemindeOfFour or $yearRemindeOfFour == 1)) ? 78 : (($leapDay == 3 and $yearRemindeOfFour == 0) ? 80 : 79);
        if((int)(($jalaliYear - 19) / 63) == 20) {
            $after--;
            $before++;
        }
        if($jalaliDaysOfYear <= $after) {
            $gregorianYear = $jalaliYear + 621;
            $gregorianDay = $jalaliDaysOfYear + $before;
        }
        else {
            $gregorianYear = $jalaliYear + 622;
            $gregorianDay = $jalaliDaysOfYear - $after;
        }
        $gregorianDaysOfMonths = array(0, 31, ($gregorianYear % 4 == 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        foreach($gregorianDaysOfMonths as $gregorianMonth => $daysOfMonth) {
            if($gregorianDay <= $daysOfMonth) {
                break;
            }
            $gregorianDay -= $daysOfMonth;
        }
        $result = array($gregorianYear, $gregorianMonth, $gregorianDay);
        return ($glue == '' ? $result : implode($glue, $result));
    }

    /**
     * Checks to see whether the given Jalali year is leap year
     * @param int $jalaliYear Jalali year
     * @return boolean The result of leap year check
     */
    public function isLeapYear($jalaliYear)
    {
        return $jalaliYear % 33 % 4 - 1 == (int)($jalaliYear % 33 * .05);
    }
}
