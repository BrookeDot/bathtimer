// ------------
// BathTime{r}
// (c)  Brooke Dukes 2015
// ------------

/*-------------

    This file is the firmware to be upload and ran on the photon. It makes use of the particle API. 
    All original code is under the GPL v3 except where listed. 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

-------------*/

#include "neopixel/neopixel.h"
#include "PowerShield/PowerShield.h"

#define PIN 6 //led input pin
#define NEOPIXEL_COUNT 24 //how many pixels
#define maxBrightness 55 //max brightness

volatile int timerMilliseconds;
unsigned long int lastPowerCheck;
unsigned long int lastShutdownCheck;
bool shutdownOverride = false;
String timerActive = "false";
String batteryPercent;


Adafruit_NeoPixel strip = Adafruit_NeoPixel(NEOPIXEL_COUNT, PIN);

void setup() {
    
  Particle.function("settimer", getMinutes); //listen for the number of minutes
  Particle.function("shutdown", shutdown); //listen for shutdown fuction
  
  Particle.variable("status", timerActive);
  Particle.variable("battery", batteryPercent);

  batteryCheck(false);
  
  //start the shutdown timeout
  lastShutdownCheck = millis() + 1000 * 60 * 10; 
  
  //turn off the onboard LED 
  RGB.control(true);
  RGB.color(0,0,0);
  //set up our pixels
  strip.begin();
  strip.show(); // Initialize all pixels to 'off'
  strip.setBrightness(maxBrightness); //  Global max brightness 
  //run startup sequence
  init(strip.Color(0, maxBrightness, 0), 100); 
  
 
}

void loop() {
    
   if( timerMilliseconds > 0 ) { //if timer is set start the countdown
        batteryCheck(true);

        startCountdown( timerMilliseconds ); //run the countdown function
        timerMilliseconds = 0;
        
        
    }
        batteryCheck(false); //check battery function
        shutdownCheck(shutdownOverride); //check shutdown function 
}
 
/** 
  * init --  When first powered up, fill the dots one after the other with a color
  * 
  * @param c -- Colour to use
  * @parms wait -- How long to wait between each light up
  *
  */
void init(uint32_t c, uint8_t wait) {
    
    //turn run our lights to green on at a time
    for(uint16_t i=0; i<strip.numPixels(); i++) {
      strip.setPixelColor(i, c);
      strip.show();
      delay(wait);
    }
    //now turn everything off
    for(uint16_t i=0; i<strip.numPixels(); i++) {
      strip.setPixelColor(i, 0,0,0);
      strip.show();
        }
}

/** 
  * getMinutes -- gets the input from our POST request and sets the number of minutes and runs the countdown
  *  
  * @param minutes -- gets string from Particle API 
  *
  * Get the returned number of minutes to integer by using minutes.toInt() 
  * Get the number of milliseconds with * 60000 
  * Divide by 85 as we have 85 "steps" between 170 and 255 in the wheel function
  * Return the number of milliseconds in each step to be passed to StartCountdown function
  */
int getMinutes( String minutes ) {
   return (timerMilliseconds = minutes.toInt() * 60000 / 85);
}


/** 
  * startCountdown -- countdown function setting the lights from blue to red
  *  
  * @param wait -- how long to wait between each step
  *
  * Requires the Wheel() and endCountdown()
  */
void startCountdown(int wait) {
  uint16_t i, j;
  
    //set timer data
    timerActive = "true";
    
    
    
  for(j=170; j<=255; j++) {

    for(i=0; i<strip.numPixels(); i++) {
            strip.setPixelColor(i, Wheel( (j) & 255) );
      }
     strip.show();
     delay(wait);
     
    //flash vigorously to signify our loop is over
    if(j == 255){
        endCountdown(250,5);
        }
    }
}
/** 
  * endCountdown -- flash function that runs after countdown ends
  *  
  * @param wait -- how long to wait between each flash
  * @param rotations -- how many times to flash
  *
  */
void endCountdown(int wait, int rotations) {
    for ( int n=0; n < rotations; n++ ) {
            for(int i=0; i<strip.numPixels(); i++) {
                strip.setPixelColor(i, maxBrightness,maxBrightness,maxBrightness);
            }
            strip.show();
            delay(wait);
            for(int i=0; i<strip.numPixels(); i++) {
                strip.setPixelColor(i, maxBrightness,0,maxBrightness);
            }
            strip.show();
            delay(wait);
    }
    for(int i=0; i<strip.numPixels(); i++) {
      strip.setPixelColor(i, 0,0,0);
    }
    strip.show();
    
  //start the shutdown timeout
  lastShutdownCheck = millis() + 1000 * 60 * 10; //has it been 10 minutes yet?
  
  //set end status
    timerActive = "false";
}


/** 
  * batteryCheck -- Checks the status of the battery and publishes %charged
  * 
  * @param override -- Should we ignore the time check
  *
  */
void batteryCheck(bool override){
    
    if( millis() >= lastPowerCheck || override == true ){
    
        PowerShield batteryMonitor;
        Wire.begin(); 
        batteryMonitor.reset();
        batteryMonitor.quickStart();
        delay(1000);
        float stateOfCharge = round(batteryMonitor.getSoC()*10)/10.0;
        delay(250);
        
        batteryPercent = String(stateOfCharge,1); // set our variabe to one decimal place

        lastPowerCheck = millis() + 1000 * 60 * 5; //run at most once every 5 minutes
    }
}

/** 
  * ShutdownCheck -- checks if device has been inactive for 10 minutes, if so we shut down
  * 
  */
void shutdownCheck(bool override) {
    
     if( millis() >= lastShutdownCheck  || override == true){
            delay(5000);
            System.sleep(SLEEP_MODE_DEEP, 999999); //sleep for ages :) 
    }
}
/** 
  * Shutdown -- Do not pass go, do not collect $200, just go to sleep
  * 
  */
bool shutdown(String command) {
    if(command == "true"){
        return shutdownOverride = true;
    }
    else{
        return shutdownOverride = false;
    }
}

/** 
  * Wheel -- transitions smoothly between colors 
  * The colours are a transition r - g - b - back to r.
  * 
  * This function is from the NeoPixel standtest.io at:
  * https://github.com/adafruit/Adafruit_NeoPixel
  *  
  * @param WheelPos -- Input a value 0 to 255 to get a color value.
  *
  */
uint32_t Wheel(byte WheelPos) {
  WheelPos = 255 - WheelPos;
  if(WheelPos < 85) {
    return strip.Color(255 - WheelPos * 3, 0, WheelPos * 3);
  }
  if(WheelPos < 170) {
    WheelPos -= 85;
    return strip.Color(0, WheelPos * 3, 255 - WheelPos * 3);
  }
  WheelPos -= 170;
  return strip.Color(WheelPos * 3, 255 - WheelPos * 3, 0);
}