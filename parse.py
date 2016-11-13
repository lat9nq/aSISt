#!/usr/bin/env python3
import re, json

course_title  = re.compile(r"^\s*[A-Z]{2,4}\s+[0-9]{4,4}")
section_title = re.compile(r"^\d{5,5}\s+\d{3,3}")
units_pattern = re.compile(r"\(.+\)")
section_desc = re.compile(r"^[\w\s&]+$")
courses = []

class Course:
    def __init__(self, dept_mnemonic, number, name, semester, year):
        self.dept_mnemonic = dept_mnemonic
        self.number = number
        self.name = name
        self.sections = []
        self.semester = semester
        self.year = year

    def __str__(self):
        return "Dept = " + self.dept_mnemonic + "; Number = " + self.number + "; Name = " + self.name + "; Semester = " + self.semester + "; Year = " + self.year

    def append_regular_section(self, line):
        templist = self.slice_units(line)
        line_list = templist[1]
        loc = templist[0]
        units = templist[2]
        self.sections.append(Section(line_list[1], line_list[2], units, line_list[4], line_list[5], line_list[7], line_list[8], line_list[9], line_list[10], line_list[11], line_list[13], " ".join([line_list[n] for n in range(14, len(line_list) - 1)]), line_list[-1]))

    def slice_units(self, line):
        units = re.findall(units_pattern, line)[0]
        line = line.replace(units, " ! ")
        units = units[1:-1]
        line_list = line.split()
        loc = line_list.index("!")
        return [loc, line_list, units]
        
    def append_independent_section(self, line):
        templist = self.slice_units(line)
        line_list = templist[1]
        loc = templist[0]
        units = templist[2]
        self.sections.append(Section(line_list[1], " ".join([line_list[n] for n in range(2, loc)]), units, line_list[loc + 1], line_list[loc + 2], line_list[loc + 4], line_list[loc + 5], line_list[loc + 6], *["TBA" for n in range(5)]))
        
class Section:
    def __init__(self, number, description, units, status, enrollment, capacity, instructor_first, instructor_last, day, start_time, end_time, building, room_number):
        self.number = number
        self.description = description
        self.units = units
        self.status = status
        self.enrollment = enrollment
        self.capacity = capacity
        self.instructor_first = instructor_first
        self.instructor_last = instructor_last
        self.days = day
        self.start_time = start_time
        self.end_time = end_time
        self.building = building
        self.room_number = room_number

    def __str__(self):
        return "Number = " + self.number + "; Description = " + self.description + "; Units = " + self.units + "; Status = " + self.status + "; Enrollment = " + self.enrollment + "; Capacity = " + self.capacity + "; Instructor First = " + self.instructor_first + "; Instructor Last = " + self.instructor_last + "; Days = " + self.days + "; Start Time = " + self.start_time + "; End Time = " + self.end_time + "; Building = " + self.building + "; Room Number = " + self.room_number

def append_course(line_list):
    courses.append(Course(line_list[0], line_list[1], " ".join([line_list[n] for n in range(2, len(line_list) - 2)]), line_list[-2], line_list[-1]))
    

def print_courses():
    for course in courses:
        print(course)
        '''
        for i in range(len(course.sections)):
            pass
            print(course.sections[i])
        print("+" * 200)
        '''

def output_json():
    output = []
    for course in courses:
        course_dict = {"dept_mnemonic" : course.dept_mnemonic, "number" : course.number, "name" : course.name}
        output.append(course_dict)
        semester = course.semester
        dept_mnemonic = course.dept_mnemonic
        year = course.year
        course_number = course.number
        for section in course.sections:
            section_dict = {"dept_mnemonic" : dept_mnemonic, "semester" : semester, "year" : year, "number" : section.number, "description" : section.description, "units" : section.units, "status" : section.status, "enrollment" : section.enrollment, "capacity" : section.capacity, "instructor_first" : section.instructor_first, "instructor_last" : section.instructor_last, "days" : section.days, "start_time" : section.start_time, "end_time" : section.end_time, "building" : section.building, "room_number" : section.room_number, "course_number" : course_number}
            output.append(section_dict)
    print(json.dumps(output))

'''
self.number = number
        self.description = description
        self.units = units
        self.status = status
        self.enrollment = enrollment
        self.capacity = capacity
        self.instructor_first = instructor_first
        self.instructor_last = instructor_last
        self.days = day
        self.start_time = start_time
        self.end_time = end_time
        self.building = building
        self.room_number = room_number
'''
        
if __name__ == "__main__":
    files = []
    for first in ["anth", "astr", "cs", "ece"]:
        for second in ["fall", "spring"]:
            for third in ["2015", "2016"]:
                if not (second == "spring" and third == "2015"):
                    files.append(first + "_" + second + "_" + third + ".txt")
    for fi in files:
        with open(fi, "r") as f:
            title = ""
            f_list = re.findall(r"[a-zA-Z0-9]+", fi)
            for line in f:
                line = line.replace("Syllabus", "")
                line_list = line.split()
                line_list.extend([f_list[1], f_list[2]])
                if course_title.search(line):
                    append_course(line_list)
                elif section_title.search(line) and "TBA" not in line:
                    courses[-1].append_regular_section(line)
                elif section_title.search(line):
                    courses[-1].append_independent_section(line)
    output_json()
