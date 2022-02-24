<?php

if (!function_exists('_lang')) {
    function _lang($string = '') {

        $target_lang = get_language();

        if ($target_lang == '') {
            $target_lang = "language";
        }

        if (file_exists(resource_path() . "/language/$target_lang.php")) {
            include resource_path() . "/language/$target_lang.php";
        } else {
            include resource_path() . "/language/language.php";
        }

        if (array_key_exists($string, $language)) {
            return $language[$string];
        } else {
            return $string;
        }
    }
}

if (!function_exists('_dlang')) {
    function _dlang($string = '') {

        //Get Target language
        $target_lang = get_option('language');

        if ($target_lang == '') {
            $target_lang = 'language';
        }

        if (file_exists(resource_path() . "/language/$target_lang.php")) {
            include resource_path() . "/language/$target_lang.php";
        } else {
            include resource_path() . "/language/language.php";
        }

        if (array_key_exists($string, $language)) {
            return $language[$string];
        } else {
            return $string;
        }
    }
}

if (!function_exists('startsWith')) {
    function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if (!function_exists('user_id')) {
    function user_id() {
        if (Auth::check()) {
                return Auth::user()->id;
        }
        return '';
    }
}


if (!function_exists('company_id')) {
    function company_id() {
        if (Auth::check()) {
            if (Auth::user()->user_type == 'admin') {
                return '';
            }

            if (Auth::user()->company_id !== NULL) {
                return Auth::user()->company_id;
            } else {
                return Auth::user()->id;
            }
        }
        return '';
    }
}

if (!function_exists('jenis_langganan')) {
    function jenis_langganan() {
        if (Auth::check()) {
            if (Auth::user()->user_type == 'admin') {
                return '';
            }

            if (Auth::user()->jenis_langganan !== "") {
                return Auth::user()->jenis_langganan;
            } else {
                $user_admin = \App\User::where("id",company_id())->first();
                return $user_admin->jenis_langganan;
            }
        }
        return '';
    }
}

if (!function_exists('cabang')) {
    function cabang() {
        if (Auth::check()) {
            if (Auth::user()->user_type == 'admin') {
                return '';
            }

            if (Auth::user()->cabang !== NULL) {
                return Auth::user()->cabang;
            } else {
                $user_admin = \App\User::where("id",company_id())->first();
                return $user_admin->cabang;
            }
        }
        return '';
    }
}

if (!function_exists('membership_validity')) {
    function membership_validity() {
        if (Auth::user()->company_id != "") {
            $membership = DB::table('users')
                ->where('id', company_id())
                ->first();
            return $membership->valid_to;
        } else {
            $membership = DB::table('users')
                ->where('id', Auth::user()->id)
                ->first();
            return $membership->valid_to;
        }
    }
}

if (!function_exists('has_membership_system')) {
    function has_membership_system() {
        $membership_system = \Cache::get('membership_system');
        if ($membership_system == '') {
            $membership_system = get_option('membership_system');
            \Cache::put('membership_system', $membership_system);
        }

        return $membership_system;
    }
}

if (!function_exists('get_initials')) {
    function get_initials($string) {
        $words    = explode(" ", $string);
        $initials = null;
        foreach ($words as $w) {
            $initials .= $w[0];
        }
        return $initials;
    }
}

if (!function_exists('create_option')) {
    function create_option($table, $value, $display, $selected = '', $where = NULL) {
        $options   = '';
        $condition = '';
        if ($where != NULL) {
            $condition .= "WHERE ";
            foreach ($where as $key => $v) {
                $condition .= $key . "'" . $v . "' ";
            }
        }

        if (is_array($display)) {
            $display_array = $display;
            $display       = $display_array[0];
            $display1      = $display_array[1];
        }

        $query = DB::select("SELECT * FROM $table $condition");
        foreach ($query as $d) {
            if ($selected != '' && $selected == $d->$value) {
                if (!isset($display_array)) {
                    $options .= "<option value='" . $d->$value . "' selected='true'>" . ucwords($d->$display) . "</option>";
                } else {
                    $options .= "<option value='" . $d->$value . "' selected='true'>" . ucwords($d->$display . ' - ' . $d->$display1) . "</option>";
                }
            } else {
                if (!isset($display_array)) {
                    $options .= "<option value='" . $d->$value . "'>" . ucwords($d->$display) . "</option>";
                } else {
                    $options .= "<option value='" . $d->$value . "'>" . ucwords($d->$display . ' - ' . $d->$display1) . "</option>";
                }
            }
        }

        echo $options;
    }
}

if (!function_exists('object_to_string')) {
    function object_to_string($object, $col, $quote = false) {
        $string = "";
        foreach ($object as $data) {
            if ($quote == true) {
                $string .= "'" . $data->$col . "', ";
            } else {
                $string .= $data->$col . ", ";
            }
        }
        $string = substr_replace($string, "", -2);
        return $string;
    }
}

if (!function_exists('get_table')) {
    function get_table($table, $where = NULL) {
        $condition = "";
        if ($where != NULL) {
            $condition .= "WHERE ";
            foreach ($where as $key => $v) {
                $condition .= $key . "'" . $v . "' ";
            }
        }
        $query = DB::select("SELECT * FROM $table $condition");
        return $query;
    }
}

if (!function_exists('user_count')) {
    function user_count($user_type) {
        $count = \App\User::where("user_type", $user_type)
            ->selectRaw("COUNT(id) as total")
            ->first()->total;
        return $count;
    }
}

if (!function_exists('has_permission')) {
    function has_permission($name) {
        $permission_list = \Auth::user()->role->permissions;
        $permission      = $permission_list->firstWhere('permission', $name);

        if ($permission != null) {
            return true;
        }
        return false;
    }
}

if (!function_exists('get_logo')) {
    function get_logo() {
        $logo = get_option("logo");
        if ($logo == "") {
            return asset("public/backend/images/company-logo.png");
        }
        return asset("public/uploads/media/$logo");
    }
}

if (!function_exists('get_favicon')) {
    function get_favicon() {
        $favicon = get_option("favicon");
        if ($favicon == "") {
            return asset("public/backend/images/favicon.png");
        }
        return asset("public/uploads/media/$favicon");
    }
}

if (!function_exists('get_company_logo')) {
    function get_company_logo($company_id = '') {
        if ($company_id == '') {
            $logo = get_company_option("company_logo");
        } else {
            $logo = get_company_field($company_id, "company_logo");
        }
        if ($logo == "") {
            return asset("public/backend/images/company-logo.png");
        }
        return asset("public/uploads/company/$logo");
    }
}

if (!function_exists('get_pdf_company_logo')) {
    function get_pdf_company_logo($company_id = '') {
        if ($company_id == '') {
            $logo = get_company_option("company_logo");
        } else {
            $logo = get_company_field($company_id, "company_logo");
        }
        if ($logo == "") {
            return public_path("backend/images/company-logo.png");
        }
        return public_path("uploads/company/$logo");
    }
}

if (!function_exists('profile_picture')) {
    function profile_picture($profile_picture = '') {
        if ($profile_picture == '') {
            $profile_picture = Auth::user()->profile_picture;
        }

        if ($profile_picture == '') {
            return asset('public/backend/images/avatar.png');
        }

        return asset('public/uploads/profile/' . $profile_picture);
    }
}

if (!function_exists('sql_escape')) {
    function sql_escape($unsafe_str) {
        if (get_magic_quotes_gpc()) {
            $unsafe_str = stripslashes($unsafe_str);
        }
        return $escaped_str = str_replace("'", "", $unsafe_str);
    }
}

if (!function_exists('get_option')) {
    function get_option($name, $optional = '') {
        $value = Cache::get($name);

        if ($value == "") {
            $setting = DB::table('settings')->where('name', $name)->get();
            if (!$setting->isEmpty()) {
                $value = $setting[0]->value;
                Cache::put($name, $value);
            } else {
                $value = $optional;
            }
        }
        return $value;

    }
}

if (!function_exists('get_company_option')) {
    function get_company_option($name, $optional = '') {
        $setting = DB::table('company_settings')
            ->where('name', $name)
            ->where('company_id', company_id())
            ->get();

        if (!$setting->isEmpty()) {
            return $setting[0]->value;
        }
        return $optional;

    }
}

if (!function_exists('get_company_field')) {
    function get_company_field($company_id, $name, $optional = '') {
        $setting = DB::table('company_settings')
            ->where('name', $name)
            ->where('company_id', $company_id)
            ->get();

        if (!$setting->isEmpty()) {
            return $setting[0]->value;
        }
        return $optional;

    }
}

if (!function_exists('get_setting')) {
    function get_setting($settings, $name, $optional = '') {
        $row = $settings->firstWhere('name', $name);
        if ($row != null) {
            return $row->value;
        }
        return $optional;

    }
}

if (!function_exists('get_array_option')) {
    function get_array_option($name, $key = '', $optional = '') {
        if ($key == '') {
            if (session('language') == '') {
                $key = get_option('language');
                session(['language' => $key]);
            } else {
                $key = session('language');
            }
        }
        $setting = DB::table('settings')->where('name', $name)->get();
        if (!$setting->isEmpty()) {

            $value = $setting[0]->value;
            if (@unserialize($value) !== false) {
                $value = @unserialize($setting[0]->value);

                return isset($value[$key]) ? $value[$key] : $value[array_key_first($value)];
            }

            return $value;
        }
        return $optional;

    }
}

if (!function_exists('get_array_data')) {
    function get_array_data($data, $key = '') {
        if ($key == '') {
            if (session('language') == '') {
                $key = get_option('language');
                session(['language' => $key]);
            } else {
                $key = session('language');
            }
        }

        if (@unserialize($data) !== false) {
            $value = @unserialize($data);
            return isset($value[$key]) ? $value[$key] : $value[array_key_first($value)];
        }

        return $data;

    }
}

if (!function_exists('update_option')) {
    function update_option($name, $value) {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));

        $data               = array();
        $data['value']      = $value;
        $data['updated_at'] = \Carbon\Carbon::now();
        if (\App\Setting::where('name', $name)->exists()) {
            \App\Setting::where('name', $name)->update($data);
        } else {
            $data['name']       = $name;
            $data['created_at'] = \Carbon\Carbon::now();
            \App\Setting::insert($data);
        }
    }
}

if (!function_exists('timezone_list')) {

    function timezone_list() {
        $zones_array = array();
        $timestamp   = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$key]['ZONE'] = $zone;
            $zones_array[$key]['GMT']  = 'UTC/GMT ' . date('P', $timestamp);
        }
        return $zones_array;
    }

}

if (!function_exists('create_timezone_option')) {

    function create_timezone_option($old = "") {
        $option    = "";
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $selected = $old == $zone ? "selected" : "";
            $option .= '<option value="' . $zone . '"' . $selected . '>' . 'GMT ' . date('P', $timestamp) . ' ' . $zone . '</option>';
        }
        echo $option;
    }

}

if (!function_exists('get_country_list')) {
    function get_country_list($old_data = '') {
        if ($old_data == '') {
            echo file_get_contents(app_path() . '/Helpers/country.txt');
        } else {
            $pattern      = '<option value="' . $old_data . '">';
            $replace      = '<option value="' . $old_data . '" selected="selected">';
            $country_list = file_get_contents(app_path() . '/Helpers/country.txt');
            $country_list = str_replace($pattern, $replace, $country_list);
            echo $country_list;
        }
    }
}

/* Method use for Global amount only */
if (!function_exists('g_decimal_place')) {
    function g_decimal_place($number, $symbol = '', $format = '') {
        if ($symbol == '') {
            return money_format_2($number, $format);
        }

        if ($currency_position == 'left') {
            return $symbol . ' ' . money_format_2($number, $format);
        } else {
            return money_format_2($number, $format) . ' ' . $symbol;
        }
    }
}

if (!function_exists('decimalPlace')) {
    function decimalPlace($number, $symbol = '') {
        if ($symbol == '') {
            return money_format_2($number);
        }

        if (get_currency_position() == 'right') {
            return money_format_2($number) . ' ' . $symbol;
        } else {
            return $symbol . ' ' . money_format_2($number);
        }
    }
}

if (!function_exists('money_format_2')) {
    function money_format_2($floatcurr) {
        $decimal_place = get_option('decimal_places', 2);
        $decimal_sep   = get_option('decimal_sep', '.');
        $thousand_sep  = get_option('thousand_sep', ',');

        return number_format($floatcurr, $decimal_place, $decimal_sep, $thousand_sep);
    }
}

if (!function_exists('load_language')) {
    function load_language($active = '') {
        $path    = resource_path() . "/language";
        $files   = scandir($path);
        $options = "";

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name == "." || $name == "" || $name == "language") {
                continue;
            }

            $selected = "";
            if ($active == $name) {
                $selected = "selected";
            } else {
                $selected = "";
            }

            $options .= "<option value='$name' $selected>" . $name . "</option>";

        }
        echo $options;
    }
}

if (!function_exists('get_language_list')) {
    function get_language_list() {
        $path  = resource_path() . "/language";
        $files = scandir($path);
        $array = array();

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name == "." || $name == "" || $name == "language" || $name == "flags") {
                continue;
            }

            $array[] = $name;

        }
        return $array;
    }
}

if (!function_exists('process_string')) {

    function process_string($search_replace, $string) {
        $result = $string;
        foreach ($search_replace as $key => $value) {
            $result = str_replace($key, $value, $result);
        }
        return $result;
    }

}

if (!function_exists('permission_list')) {
    function permission_list() {

        $permission_list = \App\AccessControl::where("role_id", Auth::user()->role_id)
            ->pluck('permission')->toArray();
        return $permission_list;
    }
}

if (!function_exists('get_currency_list')) {
    function get_currency_list($old_data = '', $serialize = false) {
        $currency_list = file_get_contents(app_path() . '/Helpers/currency.txt');

        if ($old_data == "") {
            echo $currency_list;
        } else {
            if ($serialize == true) {
                $old_data = unserialize($old_data);
                for ($i = 0; $i < count($old_data); $i++) {
                    $pattern       = '<option value="' . $old_data[$i] . '">';
                    $replace       = '<option value="' . $old_data[$i] . '" selected="selected">';
                    $currency_list = str_replace($pattern, $replace, $currency_list);
                }
                echo $currency_list;
            } else {
                $pattern       = '<option value="' . $old_data . '">';
                $replace       = '<option value="' . $old_data . '" selected="selected">';
                $currency_list = str_replace($pattern, $replace, $currency_list);
                echo $currency_list;
            }
        }
    }
}

if (!function_exists('get_currency_symbol')) {
    function get_currency_symbol($currency_code) {
        include app_path() . '/Helpers/currency_symbol.php';

        if (array_key_exists($currency_code, $currency_symbols)) {
            return $currency_symbols[$currency_code];
        }
        return "";

    }
}

if (!function_exists('status')) {
    function status($status, $class = 'success') {
        if ($class == 'danger') {
            return "<span class='badge badge-danger'>$status</span>";
        } else if ($class == 'success') {
            return "<span class='badge badge-success'>$status</span>";
        } else if ($class == 'info') {
            return "<span class='badge badge-dark'>$status</span>";
        }
    }
}

if (!function_exists('user_status')) {
    function user_status($status) {
        if ($status == 1) {
            return "<span class='badge badge-danger'>" . _lang('Active') . "</span>";
        } else if ($status == 0) {
            return "<span class='badge badge-success'>" . _lang('In Active') . "</span>";
        }
    }
}

if (!function_exists('file_icon')) {
    function file_icon($mime_type) {
        static $font_awesome_file_icon_classes = [
            // Images
            'image'                                                                     => 'fa-file-image',
            // Audio
            'audio'                                                                     => 'fa-file-audio',
            // Video
            'video'                                                                     => 'fa-file-video',
            // Documents
            'application/pdf'                                                           => 'fa-file-pdf',
            'application/msword'                                                        => 'fa-file-word',
            'application/vnd.ms-word'                                                   => 'fa-file-word',
            'application/vnd.oasis.opendocument.text'                                   => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml'            => 'fa-file-word',
            'application/vnd.ms-excel'                                                  => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml'               => 'fa-file-excel',
            'application/vnd.oasis.opendocument.spreadsheet'                            => 'fa-file-excel',
            'application/vnd.ms-powerpoint'                                             => 'fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml'              => 'ffa-file-powerpoint',
            'application/vnd.oasis.opendocument.presentation'                           => 'fa-file-powerpoint',
            'text/plain'                                                                => 'fa-file-alt',
            'text/html'                                                                 => 'fa-file-code',
            'application/json'                                                          => 'fa-file-code',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint',
            // Archives
            'application/gzip'                                                          => 'fa-file-archive',
            'application/zip'                                                           => 'fa-file-archive',
            'application/x-zip-compressed'                                              => 'fa-file-archive',
            // Misc
            'application/octet-stream'                                                  => 'fa-file-archive',
        ];

        if (isset($font_awesome_file_icon_classes[$mime_type])) {
            return $font_awesome_file_icon_classes[$mime_type];
        }

        $mime_group = explode('/', $mime_type, 2)[0];
        return (isset($font_awesome_file_icon_classes[$mime_group])) ? $font_awesome_file_icon_classes[$mime_group] : 'fa-file';
    }
}

if (!function_exists('xss_clean')) {
    function xss_clean($data) {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data     = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
}

// convert seconds into time
if (!function_exists('time_from_seconds')) {
    function time_from_seconds($seconds) {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds - ($h * 3600) - ($m * 60);
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}

if (!function_exists('current_day_income')) {
    function current_day_income() {
        $company_id = company_id();
        $date       = date("Y-m-d");

        $query = DB::select("SELECT IFNULL(SUM(amount),0) as total FROM transactions
		WHERE trans_date='$date' AND dr_cr='cr' AND company_id='$company_id'");
        return $query[0]->total;
    }
}

if (!function_exists('current_day_expense')) {
    function current_day_expense() {
        $company_id = company_id();
        $date       = date("Y-m-d");

        $query = DB::select("SELECT IFNULL(SUM(amount),0) as total FROM transactions
		WHERE trans_date='$date' AND dr_cr='dr' AND company_id='$company_id'");
        return $query[0]->total;
    }
}

if (!function_exists('current_month_income')) {
    function current_month_income() {
        $company_id = company_id();
        $month      = date('m');
        $year       = date('Y');

        $monthly_income = \App\Transaction::selectRaw("IFNULL(SUM(amount),0) as total")
            ->where("dr_cr", "cr")
            ->where("company_id", $company_id)
            ->whereMonth("trans_date", $month)
            ->whereYear("trans_date", $year)
            ->first()->total;
        return $monthly_income;
    }
}

if (!function_exists('current_month_expense')) {
    function current_month_expense() {
        $company_id = company_id();
        $month      = date('m');
        $year       = date('Y');

        $monthly_expense = \App\Transaction::selectRaw("IFNULL(SUM(amount),0) as total")
            ->where("dr_cr", "dr")
            ->where("company_id", $company_id)
            ->whereMonth("trans_date", $month)
            ->whereYear("trans_date", $year)
            ->first()->total;
        return $monthly_expense;
    }
}

if (!function_exists('get_financial_balance')) {

    function get_financial_balance() {
        $company_id = company_id();

        $result = DB::select("SELECT b.*,((SELECT IFNULL(opening_balance,0)
   FROM accounts WHERE id = b.id) + (SELECT IFNULL(SUM(amount), 0)
   FROM transactions WHERE dr_cr = 'cr' AND account_id = b.id)) - (SELECT IFNULL(SUM(amount),0)
   FROM transactions WHERE dr_cr = 'dr' AND account_id = b.id) as balance
   FROM accounts as b WHERE b.company_id = '$company_id' order by b.account_number asc");
        return $result;

    }

}

if (!function_exists('get_financial_balance_detail')) {

    function get_financial_balance_detail() {
        $company_id = company_id();

        $result = DB::select("SELECT b.*,((SELECT IFNULL(opening_balance,0)
   FROM accounts WHERE id = b.id) + (SELECT IFNULL(SUM(amount), 0)
   FROM transactions WHERE dr_cr = 'cr' AND account_id = b.id)) - (SELECT IFNULL(SUM(amount),0)
   FROM transactions WHERE dr_cr = 'dr' AND account_id = b.id) as balance
   FROM accounts as b WHERE b.company_id = '$company_id' and b.jenis='D' order by b.account_number asc");
        return $result;

    }

}

if (!function_exists('get_financial_balance_header')) {

    function get_financial_balance_header() {
        $company_id = company_id();

        $result = DB::select("SELECT b.*,((SELECT IFNULL(opening_balance,0)
   FROM accounts WHERE id = b.id) + (SELECT IFNULL(SUM(amount), 0)
   FROM transactions WHERE dr_cr = 'cr' AND account_id in (select c.id from accounts as c where left(c.account_number,2)=left(b.account_number,2))) - (SELECT IFNULL(SUM(amount),0)
   FROM transactions WHERE dr_cr = 'dr' AND account_id in (select c.id from accounts as c where left(c.account_number,2)=left(b.account_number,2)))) as balance
   FROM accounts as b WHERE b.company_id = '$company_id' and b.jenis='H' order by b.account_number asc");
        return $result;

    }

}

if (!function_exists('invoice_status')) {
    function invoice_status($status) {
        if ($status == 'Unpaid') {
            return "<span class='badge badge-danger'>$status</span>";
        } else if ($status == 'Paid') {
            return "<span class='badge badge-success'>$status</span>";
        } else if ($status == 'Partially_Paid') {
            return "<span class='badge badge-info'>" . str_replace('_', ' ', $status) . "</span>";
        } else if ($status == 'Canceled') {
            return "<span class='badge badge-danger'>$status</span>";
        }
    }
}

if (!function_exists('create_payment_method')) {
    function create_payment_method($methodName, $company_id) {
        $payment_method = \App\PaymentMethod::where('name', $methodName)->where("company_id", $company_id);
        if ($payment_method->exists()) {
            return $payment_method->first()->id;
        } else {
            $payment_method             = new \App\PaymentMethod();
            $payment_method->name       = $methodName;
            $payment_method->company_id = $company_id;
            $payment_method->save();
            return $payment_method->id;
        }
    }
}

if (!function_exists('increment_invoice_number')) {
    function increment_invoice_number() {
        $company_id         = company_id();
        $data               = array();
        $data['value']      = get_company_option('invoice_starting') + 1;
        $data['company_id'] = $company_id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (\App\CompanySetting::where('name', "invoice_starting")->where("company_id", $company_id)->exists()) {
            \App\CompanySetting::where('name', 'invoice_starting')
                ->where("company_id", $company_id)
                ->update($data);
        } else {
            $data['name']       = 'invoice_starting';
            $data['created_at'] = date('Y-m-d H:i:s');
            \App\CompanySetting::insert($data);
        }
    }
}

if (!function_exists('increment_orderpembelian_number')) {
    function increment_orderpembelian_number() {
        $company_id         = company_id();
        $data               = array();
        $data['value']      = get_company_option('invoice_order_pembelian') + 1;
        $data['company_id'] = $company_id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (\App\CompanySetting::where('name', "invoice_order_pembelian")->where("company_id", $company_id)->exists()) {
            \App\CompanySetting::where('name', 'invoice_order_pembelian')
                ->where("company_id", $company_id)
                ->update($data);
        } else {
            $data['name']       = 'invoice_order_pembelian';
            $data['created_at'] = date('Y-m-d H:i:s');
            \App\CompanySetting::insert($data);
        }
    }
}


if (!function_exists('increment_pembelian_number')) {
    function increment_pembelian_number() {
        $company_id         = company_id();
        $data               = array();
        $data['value']      = get_company_option('invoice_pembelian') + 1;
        $data['company_id'] = $company_id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (\App\CompanySetting::where('name', "invoice_pembelian")->where("company_id", $company_id)->exists()) {
            \App\CompanySetting::where('name', 'invoice_pembelian')
                ->where("company_id", $company_id)
                ->update($data);
        } else {
            $data['name']       = 'invoice_pembelian';
            $data['created_at'] = date('Y-m-d H:i:s');
            \App\CompanySetting::insert($data);
        }
    }
}

if (!function_exists('increment_quotation_number')) {
    function increment_quotation_number() {
        $company_id         = company_id();
        $data               = array();
        $data['value']      = get_company_option('quotation_starting') + 1;
        $data['company_id'] = $company_id;
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (\App\CompanySetting::where('name', "quotation_starting")->where("company_id", $company_id)->exists()) {
            \App\CompanySetting::where('name', 'quotation_starting')
                ->where("company_id", $company_id)
                ->update($data);
        } else {
            $data['name']       = 'quotation_starting';
            $data['created_at'] = date('Y-m-d H:i:s');
            \App\CompanySetting::insert($data);
        }
    }
}

if (!function_exists('update_stock')) {
    function update_stock($product_id) {
        $company_id = company_id();

        $masuk = DB::table('hpp')->where('item_id', $product_id)
            ->where("flag","<=","5")
            ->where('company_id', $company_id)
            ->sum('stok');

        $keluar = DB::table('hpp')->where('item_id', $product_id)
            ->where("flag",">","5")
            ->where('company_id', $company_id)
            ->sum('stok');
       
        //Update Stock
        $stock = \App\Stock::where("product_id", $product_id)->where("company_id", $company_id)->first();
        if ($stock) {
            $stock->quantity = ($masuk-$keluar);
            $stock->save();
        }

    }
}



if (!function_exists('update_stock_hpp')) {
    function update_stock_hpp($product_id,$gudang_id) {
        $company_id = company_id();

        $purchase = DB::table('pembelian_items')->where('product_id', $product_id)->where('gudang_id', $gudang_id)->where('company_id', $company_id)->sum('quantity');
        
        /*
        $purchaseReturn = DB::table('purchase_return_items')->where('product_id', $product_id)
            ->where('company_id', $company_id)
            ->sum('quantity');

        $sales = DB::table('invoice_items')->where('item_id', $product_id)
            ->where('company_id', $company_id)
            ->sum('quantity');

        $salesReturn = DB::table('sales_return_items')->where('product_id', $product_id)
            ->where('company_id', $company_id)
            ->sum('quantity');
        */
        
        
        //Update Stock
        $stock = \App\Hpp::where("product_id", $product_id)->where("company_id", $company_id)->where("gudang_id",$gudang)->first();
        if ($stock) {
            $stock->stok = ($purchase + $salesReturn) - ($sales + $purchaseReturn);
            $stock->save();
        }

    }
}



if (!function_exists('object_to_tax')) {
    function object_to_tax($object, $col, $quote = false) {
        if ($object->isEmpty()) {
            return _lang('N/A');
        }

        $string = "";
        foreach ($object as $data) {
            if ($quote == true) {
                $string .= "'" . $data->$col . "'<br>";
            } else {
                $string .= $data->$col . "<br>";
            }
        }
        return $string;
    }
}

/* Intelligent Functions */
if (!function_exists('get_language')) {
    function get_language() {
        $user_language = session('user_language');

        if ($user_language == '') {
            if (Auth::check()) {
                if (Auth::user()->user_type == 'user' || Auth::user()->user_type == 'staff') {
                    $user_language = get_company_option('langauge', get_option('language'));
                }

                session(['user_language' => $user_language]);
            } else {
                $user_language = get_option('language');
                session(['user_language' => $user_language]);
            }
        }

        return $user_language;
    }
}

if (!function_exists('get_currency_position')) {
    function get_currency_position() {
        $currency_position = Cache::get('currency_position');

        if ($currency_position == '') {
            $currency_position = get_option('currency_position');
            \Cache::put('currency_position', $currency_position);
        }

        return $currency_position;
    }
}

if (!function_exists('currency')) {
    function currency($currency = '') {

        if ($currency == '') {
            $currency = get_company_option('currency', get_option('currency', 'USD'));
        }

        return html_entity_decode(get_currency_symbol($currency), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('get_date_format')) {
    function get_date_format() {
        $date_format = Cache::get('date_format');

        if ($date_format == '') {
            $date_format = get_option('date_format', 'Y-m-d');
            \Cache::put('date_format', $date_format);
        }

        return $date_format;
    }
}

if (!function_exists('get_time_format')) {
    function get_time_format() {
        $time_format = Cache::get('time_format');

        if ($time_format == '') {
            $time_format = get_option('time_format');
            \Cache::put('time_format', $time_format);
        }

        $time_format = $time_format == 24 ? 'H:i' : 'h:i A';

        return $time_format;
    }
}