<?php

namespace {{ params.packageName }}\Messages;
{%- if message.description() or message.examples() %}/**{%- for line in message.description() | splitByLines %}
* {{- line | safe }}{%- endfor %}{%- if message.examples() %}
* Examples: {{- message.examples() | examplesToString | safe }}{%- endif %}
*/{%- endif %}

class {{ messageName | camelCase | upperFirst }} extends MessageContract
{
{%- for key, obj in message.payload().properties() %}
    /** @var {{- obj.type() | toPHPType }} ${{- key | camelCase }} */
    private ${{- key | camelCase }};

{%- endfor %}
{%- for key, obj in message.payload().properties() %}
    {%- set propertyType = (obj.type() | toPHPType) %}
    {%- set propertyName = (key | camelCase) %}
    {%- set propertyGetter = ( 'get' | getPropertyMethods(key) ) %}
    {%- set propertySetter = ( 'set' | getPropertyMethods(key) ) %}
    /**
     * @param {{ propertyType }} $id
     * @return MessageContract
     */
    public function {{ propertySetter }}({{ propertyType }} ${{ propertyName }}): MessageContract
    {
        $this->{{ propertyName }} = ${{ propertyName }};
        return $this;
    }

    /**
    * @return {{ propertyType }}
    */
    public function {{ propertyGetter }}(): {{ propertyType }}
    {
        return $this->{{ propertyName }};
    }
{%- endfor %}

    /**
    * @return array
    */
    public function getters(): array
    {
        return [
{%- for key, obj in message.payload().properties() %}
    {%- set propertyGetter = ( 'get' | getPropertyMethods(key) ) %}
            '{{ key }}' => '{{ propertyGetter }}',
{%- endfor %}
        ];
    }

    /**
    * @return array
    */
    public function setters(): array
    {
        return [
        {%- for key, obj in message.payload().properties() %}
        {%- set propertyGetter = ( 'set' | getPropertyMethods(key) ) %}
            '{{ key }}' => '{{ propertyGetter }}',
        {%- endfor %}
        ];
    }

    /**
    * @return array|mixed
    */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
