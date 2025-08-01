DELETE FROM `settings`
WHERE
    `name` LIKE '%recaptcha%'
    AND (
        `value` LIKE 'a:2:{s:10:"public_key";s:0:'
        OR `value` LIKE 'a:2:{s:10:"public_key";s:1:%'
        OR `value` LIKE '%6LfuqxUUAAAAAHswLzeLd_oK8sNJWRifIssc0CPB%'
        OR `value` LIKE '%6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is%'
        OR `value` LIKE '%6LeMYAkTAAAAADP0NncqOUIydMymokSI91gFxdJL%'
        OR `value` LIKE '%6LdWlgkUAAAAADzgu34FyZ-wBSB0xlCUc7UVFWGw%'
    )
