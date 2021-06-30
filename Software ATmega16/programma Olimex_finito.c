/*********************************************
  NOME FILE   : programma WiBot.c
  DESCRIZIONE : programma per la comunicazione tra Raspberry Pi e i motori del rover
  MCU	      : Atmel ATmega16 @ 8MHz
  COMPILATORE : WinAVR-20070525 (GCC 4.1.2)
  AUTORE      : Federico Corradi & Roberto Solari
  VERSIONE    : 1.0
*********************************************/
#include "common.h"
#include "uart.h"
#include <stdio.h>



unsigned char telecomando = 0 ,allerta = 0;
char comando,precedente;
unsigned char var4 = 127 , var5= 127;
unsigned char rampa=9 , velpos = 195 , velneg = 50 ; //inizialmente al 60%




/*********************************************
   FUNZIONE   : mcu_setup
   DESCRIZIONE: Inizializzazione periferiche MCU.
   PARAMETRI  : nessuno
   RETURN CODE: nessuno
*********************************************/
void mcu_setup(void)
{

 	DDRA= 0b00000000;
	PORTA=0b01111111;
	DDRB= 0b00000000;
	PORTB=0b11100001;
	DDRC= 0b00000000;
	PORTC=0b11111111;
	DDRD= 0b00110000;
	PORTD=0b11001111;


    //Inizializza UART e svuota i buffer.
	uart_init();

	// watchdog
	wdt_reset();
	wdt_disable();        //disabilita wdt

	//convertitore
	ADMUX =  0b01000111;
	ADCSRA = 0b10000110;


	//PWM

	TCCR1A= 0b10100001 ;
	TCCR1B= 0b00001001;  // frequenza pwm 30 khz
    OCR1A = var5;
	OCR1B = var4;

	// interrupt
	SEI();                //abilita interruzioni
}

/*********************************************
   FUNZIONE   : main
   DESCRIZIONE: Main loop
   PARAMETRI  : nessuno
   RETURN CODE: ignorato
*********************************************/
int main(void)
{
	mcu_setup();
	for(;;)
	{
	  //programma con telecomando manuale
      if((TST(PINB,1))&&(!TST(PINB,2))&&(TST(PINB,3))&&(TST(PINB,4)))//AVANTI
	  {
      comando = 'A';
	  telecomando = 1 ;
	  }
      if((!TST(PINB,1))&&(TST(PINB,2))&&(TST(PINB,3))&&(TST(PINB,4)))//INDIETRO
	  {
	  comando = 'I';
	  telecomando = 1 ;
	  }
      if((TST(PINB,1))&&(TST(PINB,2))&&(!TST(PINB,3))&&(TST(PINB,4)))//DESTRA
	  {
      comando = 'D';
	  telecomando = 1 ;
	  }
	  if((TST(PINB,1))&&(TST(PINB,2))&&(TST(PINB,3))&&(!TST(PINB,4)))//SINISTRA
	  {
	  comando = 'S';
	  telecomando = 1 ;
	  }
	  if((TST(PINB,1))&&(TST(PINB,2))&&(TST(PINB,3))&&(TST(PINB,4))&&(telecomando == 1))
      {comando = 'N';
	   telecomando=0;
	  }


	  //programma con pagina php
      if(uart_test()>0)
      {
	  comando = uart_get();//Assegno a "comando" il dato ricevuto
	  precedente=comando;
	  }
	  //allerta ostacoli
	  if (allerta == 1)
	  {
	   SET(ADCSRA,ADSC);
	   while(TST(ADCSRA,ADSC))
	   {
	   }

   //62 valore corrispondente a 30 cm del sensore
	   if(ADC<=62)
	   {
	      //rimangono validi solo i comandi I per nadare indietro
		  //e L per disattivare l' allerta ostacoli
		  comando = 'N';
		  if(precedente == 'I')
		  comando = 'I';
		  if(precedente == 'L')
		  comando = 'L';
	   }
	  }

	  switch (comando)
	    {

		// caso avanti
	      case 'A':

					     var4 = (var4 + var5) / 2 ;  //stabilizza la vettura nel caso in cui si provenga da una curva
						 var5 = var4 ;
						 OCR1A = var5;
	                     OCR1B = var4;
						 if (var4 < 127)
						 {
						 var4 = 127;
						 var5 = var4 ;
                         OCR1A = var5;
	                     OCR1B = var4;
						 }
					     if (var5+ rampa > velpos)
						 {
						 var5 = velpos ;
						 var4 = var5 ;
						 }
						 else
					     {
						 var5 = var5 + rampa ;
						 var4 = var5 ;
						 }
                        OCR1A = var5;
	                    OCR1B = var4;
						delayMs(100);
					   break ;


          //caso macchina ferma
           case 'N':

					     var4 = 127;
						 var5 = 127;
                         OCR1A = var5;
	                     OCR1B = var4;
          			   break ;

          //caso indietro
           case 'I':

					     var4 = (var4 + var5) / 2 ; // stabilizza la vettura
						 var5 = var4 ;
						 OCR1A = var5;
	                     OCR1B = var4;
						 if (var4 > 127)
						 {
						 var4 = 127;
						 var5 = var4 ;
                         OCR1A = var5;
	                     OCR1B = var4;
						 }
					     if (var5-rampa < velneg)
						 {
						 var5 = velneg ;
						 var4 = var5 ;
						 }
						 else
					     {
						 var5 = var5 - rampa ;
						 var4 = var5 ;
						 }

	 					 OCR1A = var5;
	                     OCR1B = var4;
			         	 delayMs(100);
					   break ;

            //per i comandi destra e sinistra considero PD4 comandante i motori di sinistra e PD5 quelli di destra

			case 'D' :
						   var4 = 190 ;
						   var5 = 127 ;
                           OCR1A = var5;
	                       OCR1B = var4;
						   break;



            case 'S' :
						   var4 = 127 ;
						   var5 = 190 ;
                           OCR1A = var5;
	                       OCR1B = var4;
                           break;



            // casi di avanti destra e avanti sinistra




	    	//avanti destra
			case 'B' :   if (var4 < 127)
						 {
						 var4 = 127;
						 var5 = var4 ;
                         OCR1A = var5;
	                     OCR1B = var4;
						 }
						 if(var5 > 230)
						 var5=230;
						 if ((var4+5)>255)
						 var4=255;
						 else
						 var4 = var4+5;
						 OCR1B = var4;
                         delayMs(20);
						 break ;

           //avanti sinistra

           case 'C' :   if (var4 < 127)
						 {
						 var4 = 127;
						 var5 = var4 ;
                         OCR1A = var5;
	                     OCR1B = var4;
						 }
                        if (var4>230)
						var4=230;
		                if((var5+5)>255)
                        var5=255;
						else
						var5=var5+5;
			            OCR1A = var5;
                        delayMs(20);
						 break ;



		   // casi di indietro destra e indietro sinistra

		   //indietro destra

		   case 'F' :    if (var4 > 127)
						 {
						 var4 = 127;
						 var5 = var4 ;
                         OCR1A = var5;
	                     OCR1B = var4;
						 }
						 if(var5<20)
						 var5 = 20;
		                 if ((var4-5)<0)
						 var4=0;
						 else
						 var4 = var4-5;
						 OCR1B = var4;
                         delayMs(20);
						 break ;


           //indietro sinistra


		   case 'G' :   if (var4 > 127)
						 {
						 var4 = 127;
						 var5 = var4 ;
                         OCR1A = var5;
	                     OCR1B = var4;
						 }
						 if(var4<20)
						 var4=20;
		                if((var5-5)<0)
                        var5=0;
						else
						var5=var5-5;
			            OCR1A = var5;
                        delayMs(20);
						 break ;






          // inizio rampa


             case 'P':   rampa = 2 ; //20%
			             break;
			 case 'Q':   rampa = 3 ; //40%
                         break;
			 case 'R':   rampa = 4 ; //60%
			             break;
			 case 'T':   rampa = 5; //80%
			             break;
			 case 'V':   rampa = 7; //100%
			             break;

           //inizio velocità
		     case 'Z': velneg = 0;   //100%
			           velpos = 255;
					   break;
			 case 'J': velneg = 25;  //80%
			           velpos = 220;
					   break;
			 case'K': velneg = 50;  //60%
			           velpos = 195;
					   break;
			 case'Y': velneg = 75;  //40%
			           velpos = 170;
					   break;
			 case'W': velneg = 100; //20%
			           velpos = 145;
					   break;

          //allerta ostacoli

		    case 'H': allerta = 1 ;
			          break ;
			case 'L': allerta = 0 ;
			          break ;

	    }

	}


}
