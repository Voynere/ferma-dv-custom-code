<?php
/**
 * Created by PhpStorm.
 * User: mswoo
 * Date: 23.05.2018
 * Time: 11:16
 */

class Wdc_Options
{

    /**
     * @var
     */
    private $page;

    /**
     * @var
     */
    private $section;

    /**
     * @var
     */
    private $option_id;

    /**
     * @var
     */
    private $option;

    /**
     * @var array
     */
    private $fields = array(
        array(
            'label' => 'text',
            'id' => '1',
            'type' => 'text',
            'section' => 'wph_custom_section',
            'desc' => 'text',
            'placeholder' => 'text',
        ),
        array(
            'label' => 'select',
            'id' => '2',
            'type' => 'select',
            'section' => 'wph_custom_section',
            'options' => array(
                'weeeeeeeeeeee:wwwwwwwwww' => 'weeeeeeeeeeee:wwwwwwwwww',
                'wwwwwwwwwwww:wwwwwwwwwww' => 'wwwwwwwwwwww:wwwwwwwwwww',
                'Wwwwwwwwwwww:wwwwwwwwwww' => 'Wwwwwwwwwwww:wwwwwwwwwww',
            ),
            'desc' => 'select',
            'placeholder' => 'select',
        ),
        array(
            'label' => 'radio',
            'id' => '4',
            'type' => 'radio',
            'section' => 'wph_custom_section',
            'options' => array(
                'frrrrrrrrrrr:' => 'frrrrrrrrrrr:',
                'rffffffffffff' => 'rffffffffffff',
            ),
            'desc' => 'radio',
            'placeholder' => 'radio',
        ),
        array(
            'label' => 'checkbox',
            'id' => '5',
            'type' => 'checkbox',
            'section' => 'wph_custom_section',
            'options' => array(
                'ffffffffff' => 'ffffffffff',
            ),
            'desc' => 'checkbox',
            'placeholder' => 'checkbox',
        ),
        array(
            'label' => 'email',
            'id' => '4444',
            'type' => 'email',
            'section' => 'wph_custom_section',
            'desc' => 'email',
            'placeholder' => 'email',
        ),
        array(
            'label' => 'password',
            'id' => '6666666',
            'type' => 'password',
            'section' => 'wph_custom_section',
            'desc' => 'password',
            'placeholder' => 'password',
        ),
    );

    /**
     * @var string
     */
    private $action;
    
    
    private $sanitize_callback;


    /**
     * Wdc_Options constructor.
     *
     * @param $page
     * @param string $action
     */
    public function __construct($page, $action = '')
    {
        $this->page = $page;
        $this->action = $action;
    }

    /**
     *
     */
    public function create()
    {
        if(!empty($this->option_id)){
            $this->option = get_option($this->option_id);
        }

        add_action($this->action, array($this, 'setup_page'));
        add_action('admin_init', array($this, 'setup_sections'));
        add_action('admin_init', array($this, 'setup_fields'));
    }

    /**
     *
     */
    public function setup_page()
    { ?>
        <div class="wrap">
            <form method="POST" action="options.php">
                <?php
                settings_fields($this->page);
                do_settings_sections($this->page);
                submit_button('', 'btn btn-success', $this->page . '_button');
                ?>
            </form>
        </div> <?php
    }

    /**
     *
     */
    public function setup_sections()
    {
        add_settings_section($this->get_section(), '', array(), $this->page);
    }

    /**
     *
     */
    public function setup_fields()
    {
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            add_settings_field($field['id'], $field['label'], array($this, 'field_callback'), $this->page, $this->get_section(), $field);
            if(!isset($field['option_id'])){
                register_setting($this->page, $field['id']);
            }

        }
        if(!empty($this->option_id)){
            register_setting($this->page, $this->option_id, $this->sanitize_callback);
        }
    }

    /**
     * @param $field
     */
    public function field_callback($field)
    {
        $value = 'false';
        if(isset($field['option_id']) and !empty($this->option_id)){
            if (isset( $this->option[$field['id']] )) {
                $value = $this->option[$field['id']];
            }
        }else{
            $value = get_option($field['id']);
        }
        $name = !empty($field['option_id']) ? $field['option_id'] . '[' .$field['id'] . ']' : $field['id'];
        printf('<div class="form-group">');
        switch ($field['type']) {
            case 'radio':
            case 'checkbox':

                if (!empty ($field['options']) && is_array($field['options'])) {
                    $options_markup = '';
                    $iterator = 0;
                    $name_input = $name . '[]';
                    foreach ($field['options'] as $key => $label) {
                        $name_input = $key == 'on' ? $name : $name_input;

                        if($key !== 'on' and !is_array($value)){
                            $value = array('false');
                        }
                        $checked = $key == 'on' ? checked($value, $key, false) : checked($value[array_search($key, $value, true)], $key, false);

                        $iterator++;
                        $options_markup .= sprintf('<input class="form-control %8$s" id="%1$s_%6$s" name="%7$s" type="%2$s" value="%3$s" %4$s /><label for="%1$s_%6$s"></label> %5$s</br></br>',
                            $field['id'],
                            $field['type'],
                            $key,
                            $checked,
                            $label,
                            $iterator,
                            $name_input,
                            $field['desing']
                        );
                    }
                    printf('<fieldset>%s</fieldset>',
                        $options_markup
                    );
                }
                break;
            case 'select':
                if (!empty ($field['options']) && is_array($field['options'])) {
                    $attr = '';
                    $options = '';
                    foreach ($field['options'] as $key => $label) {
                        $options .= sprintf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($value, $key, false),
                            $label
                        );
                    }
                    if ($field['type'] === 'multiselect') {
                        $attr = ' multiple="multiple" ';
                    }
                    printf('<select class="form-control" name="%4$s" id="%1$s" %2$s>%3$s</select>',
                        $field['id'],
                        $attr,
                        $options,
                        $name
                    );
                }
                break;
            default:
                printf('<input  class="form-control" name="%5$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
                    $field['id'],
                    $field['type'],
                    $field['placeholder'],
                    $value,
                    $name
                );
        }
        if ($desc = $field['desc']) {
            printf('<p class="description">%s </p>', $desc);
        }
        printf('</div>');
    }

    /**
     * @return mixed
     */
    public function get_section()
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     */
    public function set_section($section)
    {
        $this->section = $section;
    }

    public function get_option_id()
    {
        return $this->option_id;
    }

    /**
     * @param mixed $section
     */
    public function set_option_id($option_id)
    {
        $this->option_id = $option_id;
    }
    
    public function set_sanitize_callback($sanitize_callback)
    {
        $this->sanitize_callback = $sanitize_callback;
    }

    /**
     * @return array
     */
    public function get_fields()
    {
        return apply_filters('wdc_admin_option_fields', $this->fields,  $this->option_id, $this->section, $this->page);
    }

    /**
     * @param array $fields
     */
    public function set_fields($fields)
    {
        $this->fields = $fields;
    }

}