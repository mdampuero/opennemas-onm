/***********************************************************
 * make sure to chmod +s the binary after compiling:
 * cc -o httpd_reload httpd_reload.c ; chmod +s httpd_reload
 ***********************************************************/

#include <stdio.h>
#include <stdlib.h>

int main() {
  if (!setuid(geteuid())) {
    system("/bin/echo '/sbin/service httpd reload > /dev/null 2>&1' | /usr/bin/at now");
  } else {
    printf("Couldn't set UID to effective UID\n");
    return 1;
  }
  return 0;
}
