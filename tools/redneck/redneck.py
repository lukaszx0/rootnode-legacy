#!/usr/bin/env python
# -*- coding: utf-8 -*-
# 
# Redneck server management CLI
# Rootnode http://rootnode.net
# 
# Copyright (C) 2011 Kuba Moron

import serial
import sys
import time
from threading import Thread
#import thread

#conf:
port = "/dev/ttyUSB0"

msg = ["--->   [0] null", 
	"--->   [27-01] Device start at power on",
	"--->   [28-02] WARNING: Device restarted by watchdog",
	"--->   [15-03] Going to restart...",
	"--->   [32-04] Value restored succesfuly!",
	"--->   [33-05] ERROR: Value not restored!",
	"--->   [26-06] ERROR: Previous command not received!",
	"--->   [29-07] ERROR: Bad command!",
	"--->   [24-08] ERROR: Bad checksum!",
	"--->   [25-09] WARNING: Busy (wait for code 12)",
	"--->   [08-10] Command Accepted",
	"--->   [14-11] Nothing to do",
	"--->   [23-12] DONE!",
	"--->   [30-13] Starting ATX...",
	"--->   [20-14] WARNING: ATX cannot be ON, automatic power ON is active",
	"--->   [12=15] ERROR: ATX previously should be ON, but is still OFF!",
	"--->   [18-16] ERROR: ATX already is ON, but previously should be OFF!",
	"--->   [16-17] ERROR: ATX pgood is low - ATX is damaged!",
	"--->   [09-18] ATX ON OK",
	"--->   [31-19] Turning ATX off...",
	"--->   [10-20] ERROR: ATX previously should be OFF, but is still ON!",
	"--->   [19-21] ERROR: ATX already is OFF, but previously should be ON!",
	"--->   [17-22] ERROR: ATX pgood is still high - ATX won't OFF!",
	"--->   [11-23] ATX OFF OK",
	"--->   [01-24] Starting MB (switch pressed)...", 
	"--->   [02-25] MB Cannot be ON -> ATX is OFF",
	"--->   [03-26] unused",
	"--->   [13-27] ERROR: MB previously should be ON, but is still OFF!",
	"--->   [21-28] ERROR: MB already is ON, but previously should be OFF!",
	"--->   [06-29] ERROR: MB not responding",
	"--->   [07-30] MB ON OK",
	"--->   [34-31] Turning MB off...",
	"--->   [05-32] ERROR: MB previously should be OFF, but is still ON!",
	"--->   [22-33] ERROR: MB already is OFF, but previously should be ON!",
	"--->   [04-34] MB OFF OK",
	"--->   [xx-35] Internal power ok",
	"--->   [xx-36] ERROR: Missing internal power!",
	"--->   [xx-37] ERROR: Overheat alarm. All off!",
	"--->   [xx-39] Temperature is lower then alarm level."
	]

ser = serial.Serial(port, 19200, timeout=15)

comask=""
exit=False

def myfunc():
	while True:
		wa = ser.inWaiting()
		if wa>0:
			xa=ser.read(1)
			if ord(xa)<40:
				print(msg[ord(xa)])
			#print ord(xa)
		time.sleep(0.1)


t = Thread(target=myfunc)
t.start()


while not exit:
	print ""

	while True:
		uin = raw_input(comask)
		if len(uin)!=0:
			break;
			
	print ""

	was=False
	wa = ser.inWaiting()
	while wa>0:
		was=True
		xa=ser.read(1)
		print(msg[ord(xa)])
		wa = ser.inWaiting()
	
	if was:
		print ""
	
	comm = uin.split(' ')		
		 
	ok=1 
	nob=1
	com=val=sum=0

	#rozkazy i kody rozkazow
	command = comm[0]	
	if command == "exit":
		break;
	elif command == "mbon":
		com = 0
		nob = 1
	elif command == "mbof":
		com = 1
		nob = 1
	elif command == "pson":
		com = 2
		nob = 1
	elif command == "psof":
		com = 3
		nob = 1
	elif command == "stat":
		com = 4
		nob = 1	
	elif command == "conf":
		com = 5
		nob = 1	
	elif command == "clr":
		com=7
		nob=1
	elif command == "ls":
		com = 4
		nob = 1	
	elif command == "rst":
		com=6
		nob=1
	elif command == "cool":
		com=8
		nob=1
	elif command == "temp":
		com=9
		nob=1
	elif command == "stemp":
		com=10
		nob=1
	else:
		ok = 0
		
	#czy argument 2 jest int
	if len(comm) == 2:
		try:
			val = int(comm[1])
		except ValueError:
			ok = 0
	else:
		val=0
	
	if command=="stemp":
		val=int(val/2)
	
	if ok == 1:	
		sum = (com+val	)%64
		
		print "command: %d  value %d (sended: %d,%d,%d)" % (com, val, com + 128, val + 64, sum + 64)
		ser.write(chr(com + 128) + chr(val + 64) + chr(sum + 64))	
		
		print("Waiting for response....")	
		#x = ser.read(nob)
		
		x = ser.read(nob)

		if len(x) == 0:
			print("Server not responding!")

		
		if command!="stat" and command!="ls" and command!="cool" and command!="temp":
			if len(x)>0:
				while ord(x)!=12 and ord(x)!=9:
					print(msg[ord(x)])
					x = ser.read(1)
				print(msg[12])
		
			
		if command == "stat" or command=="ls" or command=="cool" or command=="temp":
			if len(x)>0:
				while ord(x)!=255:
					print(msg[ord(x)])
					if ord(x)==12:
						break
					if ord(x)==9:
						break
					x = ser.read(nob)
					if len(x) == 0:
						break
				
				if command=="cool":
					if val==32:
						nob=65
					else:
						nob=3
				elif command=="temp":
					nob=4
				else:
					nob=12
					
				x = ser.read(nob)
				
			else:
				print "Not enough data..."
				
			
			if command=="temp":
				temp = (ord(x[0])+ord(x[1])*256)/10.
				print "Temp      : %f" % (temp)
				temp = (ord(x[2])+ord(x[3])*256)/10.
				print "Temp alarm: %f" % (temp)
			
			if command=="cool":
				i=0
				lsb=1
				#print len(x)
				for a in x:
					if lsb==1:
						y=ord(a)
						lsb=0
					else:
						lsb=1
						if val==32:
							print "Cooler %d: %d rpm" % (i,(y+ord(a)*256)*6)
						else:
							print "Cooler %d: %d rpm" % (val,(y+ord(a)*256)*6)
						i+=1
					if i==val:
						break
				print ""
				print "Reading cooler: %d" % (ord(x[len(x)-1]))
			
			if command=="stat" or command=="ls":
				if len(x)==12:
					b = ord(x[0])
					
					atx = "OK!"
					if (b/16)%2 == b%2:
						atx = "FAIL!"
					#print "ATX[1] (MB 0...3) PG: %d ON/OFF: %d   %s" % ((b/16)%2,b%2,atx)
					print ""
					
					b = ord(x[0])
					print "ATX pgood : %d%d%d%d" % ((b&128)/128,(b&64)/64,(b&32)/32,(b&16)/16)
					print "ATX on    : %d%d%d%d" % ((b&8)/8,(b&4)/4,(b&2)/2,b&1)
					
					b = ord(x[1])
					print "ATX req   : %d%d%d%d " % ((b&8)/8,(b&4)/4,(b&2)/2,b&1)
					
					print ""
					
					b = ord(x[2])
					print "VCC pgood : %d%d" % ((b&2)/2,b&1)
					
					print""
					
					b = ord(x[4])
					c = ord(x[3])
					print "MB req    : %d%d%d%d %d%d%d%d% d%d%d%d% d%d%d%d" % ((b&128)/128,(b&64)/64,(b&32)/32,(b&16)/16,(b&8)/8,(b&4)/4,(b&2)/2,b&1,(c&128)/128,(c&64)/64,(c&32)/32,(c&16)/16,(c&8)/8,(c&4)/4,(c&2)/2,c&1)
					
					b = ord(x[6])
					c = ord(x[5])
					print "MB ps_on  : %d%d%d%d %d%d%d%d% d%d%d%d% d%d%d%d" % ((b&128)/128,(b&64)/64,(b&32)/32,(b&16)/16,(b&8)/8,(b&4)/4,(b&2)/2,b&1,(c&128)/128,(c&64)/64,(c&32)/32,(c&16)/16,(c&8)/8,(c&4)/4,(c&2)/2,c&1)
					
					b = ord(x[8])
					c = ord(x[7])
					print "MB power  : %d%d%d%d %d%d%d%d% d%d%d%d% d%d%d%d" % ((b&128)/128,(b&64)/64,(b&32)/32,(b&16)/16,(b&8)/8,(b&4)/4,(b&2)/2,b&1,(c&128)/128,(c&64)/64,(c&32)/32,(c&16)/16,(c&8)/8,(c&4)/4,(c&2)/2,c&1)
					
					b = ord(x[10])
					c = ord(x[9])
					print "MB Autooff: %d%d%d%d %d%d%d%d% d%d%d%d% d%d%d%d" % ((b&128)/128,(b&64)/64,(b&32)/32,(b&16)/16,(b&8)/8,(b&4)/4,(b&2)/2,b&1,(c&128)/128,(c&64)/64,(c&32)/32,(c&16)/16,(c&8)/8,(c&4)/4,(c&2)/2,c&1)
					
					print ""
					
					b = ord(x[11])
					print "ATX aoff  : %d" % (b&1)
					print "Quiet     : %d" % ((b&2)/2)
					print "On pwr on : %d%d" % ((b&8)/8, (b&4)/4)
					print "Overheat  : %d" % ((b&16)/16)
					print "All off T : %d" % ((b&32)/32)
					print "DS error  : %d" % ((b&64)/64)
					print "Watchdog  : %d" % ((b&128)/128)
					
					#print ord(x[12])
				else:
					print "Not enough data..."

				
	else:
		print("Invalid parameters. Must be: serw.py [command] [value]")
		#print wszystkie parametry

ser.close()
