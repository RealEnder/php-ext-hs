/*
  Copyright (c) 2014 Alex Stanev <alex@stanev.org>

  Permission is hereby granted, free of charge, to any person obtaining
  a copy of this software and associated documentation files (the
  'Software'), to deal in the Software without restriction, including
  without limitation the rights to use, copy, modify, merge, publish,
  distribute, sublicense, and/or sell copies of the Software, and to
  permit persons to whom the Software is furnished to do so, subject to
  the following conditions:

  The above copyright notice and this permission notice shall be
  included in all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
  IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
  CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
  TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
  SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "php_hs.h"

/* heatshrink */
#include "heatshrink/heatshrink_encoder.h"
#include "heatshrink/heatshrink_decoder.h"

#define DEF_WINDOW_SZ2 8
#define DEF_LOOKAHEAD_SZ2 4
#define DEF_DECODER_INPUT_BUFFER_SIZE 256
#define DEF_BUFFER_SIZE (64 * 1024)

static ZEND_FUNCTION(hs_compress);
static ZEND_FUNCTION(hs_decompress);

ZEND_BEGIN_ARG_INFO_EX(arginfo_hs_compress, 0, 0, 1)
    ZEND_ARG_INFO(0, data)
    ZEND_ARG_INFO(0, window)
    ZEND_ARG_INFO(0, lookahead)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_hs_decompress, 0, 0, 1)
    ZEND_ARG_INFO(0, data)
    ZEND_ARG_INFO(0, window)
    ZEND_ARG_INFO(0, lookahead)
ZEND_END_ARG_INFO()

static zend_function_entry hs_functions[] = {
    ZEND_FE(hs_compress, arginfo_hs_compress)
    ZEND_FE(hs_decompress, arginfo_hs_decompress)
    ZEND_FE_END
};

ZEND_MINFO_FUNCTION(hs)
{
    char buffer[128];
    php_info_print_table_start();
    php_info_print_table_row(2, "heatshrink support", "enabled");
    php_info_print_table_row(2, "Extension Version", HS_EXT_VERSION);
    snprintf(buffer, 128, "%d.%d.%d",
             HEATSHRINK_VERSION_MAJOR, HEATSHRINK_VERSION_MINOR, HEATSHRINK_VERSION_PATCH);
    php_info_print_table_row(2, "Interface Version", buffer);
    php_info_print_table_end();
}

zend_module_entry hs_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    "hs",
    hs_functions,
    NULL,
    NULL,
    NULL,
    NULL,
    ZEND_MINFO(hs),
#if ZEND_MODULE_API_NO >= 20010901
    HS_EXT_VERSION,
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_HS
ZEND_GET_MODULE(hs)
#endif

/* PHP */
static ZEND_FUNCTION(hs_compress)
{
    zval *data;
    size_t data_len;
    size_t sink_sz = 0;
    size_t poll_sz = 0;
    HSE_sink_res sres;
    HSE_poll_res pres;
    HSE_finish_res fres;
    //Default window_sz2 and lookahead_sz2 values
    uint8_t window = DEF_WINDOW_SZ2;
    uint8_t lookahead = DEF_LOOKAHEAD_SZ2;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "z|ll", &data, &window,
                              &lookahead) == FAILURE) {
        RETURN_FALSE;
    }

    if (Z_TYPE_P(data) != IS_STRING) {
        zend_error(E_WARNING, "hs_compress : expects parameter to be string.");
        RETURN_FALSE;
    }

    //Allocate encoder
    heatshrink_encoder *hse = heatshrink_encoder_alloc(window, lookahead);
    if (hse == NULL) {
        zend_error(E_WARNING, "hs_compress : heatshrink_encoder_alloc error. Check window and lookahead values.");
        RETURN_FALSE;
    }

    data_len = Z_STRLEN_P(data);
    //TODO: think of better way to compute outbuff size
    size_t outbuff_len = data_len+1;
    unsigned char *outbuff = (unsigned char *) emalloc(outbuff_len);

    size_t sunk = 0;
    do {
        sres = heatshrink_encoder_sink(hse, (unsigned char *) &(Z_STRVAL_P(data)[sunk]), data_len - sunk, &sink_sz);
        if (sres < 0) {
            zend_error(E_WARNING, "hs_compress : heatshrink_encoder_sink error");
            efree(outbuff);
            heatshrink_encoder_free(hse);
            RETURN_FALSE;
        }
        sunk += sink_sz;
        
        do {
            do {
                pres = heatshrink_encoder_poll(hse, &outbuff[poll_sz], outbuff_len - poll_sz, &sink_sz);

                if (pres < 0) {
                    zend_error(E_WARNING, "hs_compress : heatshrink_encoder_poll error");
                    efree(outbuff);
                    heatshrink_encoder_free(hse);
                    RETURN_FALSE;
                }
                poll_sz += sink_sz;
                if (poll_sz == outbuff_len && pres == HSER_POLL_MORE) {
                    //TODO: think of bettery way to compute buffer reallocation size
                    outbuff_len += data_len/2;
                    outbuff = erealloc(outbuff, outbuff_len);
                }

            } while (pres == HSER_POLL_MORE);
            
            if (sunk == data_len) {
                fres = heatshrink_encoder_finish(hse);
                if (fres < 0) {
                    zend_error(E_WARNING, "hs_compress : heatshrink_encoder_finish error");
                    efree(outbuff);
                    heatshrink_encoder_free(hse);
                    RETURN_FALSE;
                }
                if (fres == HSER_FINISH_DONE) {
#if PHP_MAJOR_VERSION == 7
                    RETVAL_STRINGL((char *) outbuff, poll_sz);
#else
                    RETVAL_STRINGL((char *) outbuff, poll_sz, 1);
#endif
                    efree(outbuff);
                    heatshrink_encoder_free(hse);
                    return;
                }
            }
        } while (fres == HSER_FINISH_MORE);
    } while (sunk < data_len);

    zend_error(E_WARNING, "hs_compress : general error");
    efree(outbuff);
    heatshrink_encoder_free(hse);
    RETURN_FALSE;
}

static ZEND_FUNCTION(hs_decompress)
{
    zval *data;
    size_t data_len;
    size_t sink_sz = 0;
    size_t poll_sz = 0;
    HSD_sink_res sres;
    HSD_poll_res pres;
    HSD_finish_res fres;
    //Default window_sz2 and lookahead_sz2 values
    uint8_t window = DEF_WINDOW_SZ2;
    uint8_t lookahead = DEF_LOOKAHEAD_SZ2;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "z|ll", &data, &window,
                              &lookahead) == FAILURE) {
        RETURN_FALSE;
    }

    if (Z_TYPE_P(data) != IS_STRING) {
        zend_error(E_WARNING, "hs_decompress : expects parameter to be string.");
        RETURN_FALSE;
    }

    //Allocate decoder
    //TODO: How we calculate input buffer size?
    heatshrink_decoder *hsd = heatshrink_decoder_alloc(DEF_DECODER_INPUT_BUFFER_SIZE, window, lookahead);
    if (hsd == NULL) {
        zend_error(E_WARNING, "hs_decompress : heatshrink_decoder_alloc error. Check window and lookahead values.");
        RETURN_FALSE;
    }

    data_len = Z_STRLEN_P(data);
    //TODO: think of better way to compute outbuff size
    size_t outbuff_len = data_len*2;
    unsigned char *outbuff = (unsigned char *) emalloc(outbuff_len);

    size_t sunk = 0;
    do {
        sres = heatshrink_decoder_sink(hsd, (unsigned char *) &(Z_STRVAL_P(data)[sunk]), data_len - sunk, &sink_sz);
        if (sres < 0) {
            zend_error(E_WARNING, "hs_decompress : heatshrink_decoder_sink error");
            efree(outbuff);
            heatshrink_decoder_free(hsd);
            RETURN_FALSE;
        }
        sunk += sink_sz;
        
        do {
            do {
                pres = heatshrink_decoder_poll(hsd, &outbuff[poll_sz], outbuff_len - poll_sz, &sink_sz);

                if (pres < 0) {
                    zend_error(E_WARNING, "hs_compress : heatshrink_decoder_poll error");
                    efree(outbuff);
                    heatshrink_decoder_free(hsd);
                    RETURN_FALSE;
                }
                poll_sz += sink_sz;
                if (poll_sz == outbuff_len && pres == HSDR_POLL_MORE) {
                    //TODO: think of bettery way to compute buffer reallocation size
                    outbuff_len += data_len/2;
                    outbuff = erealloc(outbuff, outbuff_len);
                }

            } while (pres == HSDR_POLL_MORE);
            
            if (sunk == data_len) {
                fres = heatshrink_decoder_finish(hsd);
                if (fres < 0) {
                    zend_error(E_WARNING, "hs_decompress : heatshrink_decoder_finish error");
                    efree(outbuff);
                    heatshrink_decoder_free(hsd);
                    RETURN_FALSE;
                }
                if (fres == HSDR_FINISH_DONE) {
#if PHP_MAJOR_VERSION == 7
                    RETVAL_STRINGL((char *) outbuff, poll_sz);
#else
                    RETVAL_STRINGL((char *) outbuff, poll_sz, 1);
#endif
                    efree(outbuff);
                    heatshrink_decoder_free(hsd);
                    return;
                }
            }
        } while (fres == HSDR_FINISH_MORE);
    } while (sunk < data_len);

    zend_error(E_WARNING, "hs_decompress : general error");
    efree(outbuff);
    heatshrink_decoder_free(hsd);
    RETURN_FALSE;
}
